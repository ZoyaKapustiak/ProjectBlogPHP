<?php

namespace ZoiaProjects\ProjectBlog\Blog\Commands\Users;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\InvalidArgumentException;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\Person\Name;

class UpdateUser extends Command
{
    public function __construct(
        private readonly UsersRepositoryInterface $usersRepository
    )
    {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this->setName('user:update')
            ->setDescription('Updates a user')
            ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'UUID of a user to update'
            )
            ->addOption(
                'firstName',
                'f',
                InputOption::VALUE_OPTIONAL,
                'First name'
            )
            ->addOption(
                'lastName',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Last name'
            );
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $firstName = $input->getOption('firstName');
        $lastName = $input->getOption('lastName');

        if(empty($firstName) && empty($lastName)) {
            $output->writeln('Nothing to update');
            return Command::SUCCESS;
        }
        $uuid = new UUID($input->getArgument('uuid'));

        $user = $this->usersRepository->getByUUID($uuid);
        $updateName = new Name(
            empty($firstName) ? $user->name()->first() : $firstName,
            empty($lastName) ? $user->name()->last() : $lastName
        );
        $updateUser = new User(
            $uuid,
            $updateName,
            $user->getLogin(),
            $user->hashedPassword()
        );
        $this->usersRepository->save($updateUser);
        $output->writeln('User update: ' . $uuid);
        return Command::SUCCESS;
    }
}