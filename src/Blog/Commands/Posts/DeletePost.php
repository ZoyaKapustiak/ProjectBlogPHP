<?php

namespace ZoiaProjects\ProjectBlog\Blog\Commands\Posts;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\PostNotFoundException;
use ZoiaProjects\ProjectBlog\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\UUID;

class DeletePost extends Command
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository
    )
    {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this->setName('posts:delete')
            ->setDescription('Deletes a post')
            ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'UUID of a post to delete'
            )
            ->addOption(
            // Имя опции
                'check-existence',
                // Сокращённое имя
                'c',
                // Опция не имеет значения
                InputOption::VALUE_NONE,
                // Описание
                'Check if post actually exists',
            );
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $question = new ConfirmationQuestion(
            'Delete post [Y/n]? ',
            false
        );
        if(!$this->getHelper('question')
        ->ask($input, $output, $question)
        ) {
            return Command::SUCCESS;
        }
        $uuid = new UUID($input->getArgument('uuid'));
        // Если опция проверки существования статьи установлена
        if ($input->getOption('check-existence')) {
            try {
// Пытаемся получить статью
                $this->postsRepository->getByUUID($uuid);
            } catch (PostNotFoundException $e) {
// Выходим, если статья не найдена
                $output->writeln($e->getMessage());
                return Command::FAILURE;
            }
        }
        // Удаляем статью из репозитория
        $this->postsRepository->delete($uuid);
        $output->writeln("Post $uuid deleted");
        return Command::SUCCESS;
    }
}