<?php

namespace ZoiaProjects\ProjectBlog\Blog\Commands\Users;



use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\UserNotFoundException;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Person\Name;

class CreateUser extends Command
{
    public function __construct(
        private readonly UsersRepositoryInterface $usersRepository
    ){
        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            // Указываем имя команды;
            // мы будем запускать команду,
            // используя это имя
            ->setName('users:create')

            // Описание команды
            ->setDescription('Creates new user')

            // Перечисляем аргументы команды
            ->addArgument(
            // Имя аргумента;
            // его значение будет доступно
            // по этому имени
                'firstName',
                // Указание того,
                // что аргумент обязательный
                InputArgument::REQUIRED,
                // Описание аргумента
                'First name'
            )
            // Описываем остальные аргументы
            ->addArgument('lastName', InputArgument::REQUIRED, 'Last name')
            ->addArgument('login', InputArgument::REQUIRED, 'Username')
            ->addArgument('password', InputArgument::REQUIRED, 'Password');
    }
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int
    {
        // Для вывода сообщения вместо логгера
        // используем объект типа OutputInterface
        $output->writeln('Create user command started');

        // Вместо использования нашего класса Arguments
        // получаем аргументы из объекта типа InputInterface
        $login = $input->getArgument('login');
        if ($this->userExists($login)) {

            // Используем OutputInterface вместо логгера
            $output->writeln("User already exists: $login");

            // Завершаем команду с ошибкой
            return Command::FAILURE;
        }

        // Перенесли из класса CreateUserCommand
        // Вместо Arguments используем InputInterface
        $user = User::createFrom(
            $login,
            new Name(
                $input->getArgument('firstName'),
                $input->getArgument('lastName')
            ),
            $input->getArgument('password'),
        );

        $this->usersRepository->save($user);
        // Используем OutputInterface вместо логгера
        $output->writeln('User created: ' . $user->uuid());
        // Возвращаем код успешного завершения
        return Command::SUCCESS;
    }

    private function userExists(mixed $login): bool
    {
        try {
            $this->usersRepository->getByLogin($login);
        } catch (UserNotFoundException $exception) {
            return false;
        }
        return true;
    }

}