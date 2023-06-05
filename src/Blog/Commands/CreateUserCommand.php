<?php

namespace ZoiaProjects\ProjectBlog\Blog\Commands;

use ZoiaProjects\ProjectBlog\Blog\Exceptions\ArgumentsException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\CommandException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\InvalidArgumentException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\UserNotFoundException;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\Person\Name;
use Psr\Log\LoggerInterface;

// php cli.php login=zoiaNig firstName=Zoia lastName=Kapustiak password=123
class CreateUserCommand
{
    // Команда зависит от контракта репозитория пользователей,
// а не от конкретной реализации
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private LoggerInterface $logger
    )
    {
    }

    /**
     * @throws CommandException
     * @throws ArgumentsException
     */
    public function handle(Arguments $arguments): void
    {
        $this->logger->info("Create user command started");

        $login = $arguments->get('login');



// Проверяем, существует ли пользователь в репозитории
        if ($this->userExists($login)) {
            $this->logger->warning("User already exists: $login");
// Бросаем исключение, если пользователь уже существует
            throw new CommandException("User already exists: $login");
        }
       $user = User::createFrom(
           $login,
           new Name(
               $arguments->get('firstName'),
               $arguments->get('lastName'),
           ),
           $arguments->get('password')
       );
        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save($user);

        $this->logger->info("User created: " . $user->getLogin());
    }
    private function userExists(string $username): bool
    {
        try {
            // Пытаемся получить пользователя из репозитория
            $this->usersRepository->getByLogin($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }

}