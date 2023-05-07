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

class CreateUserCommand
{
    // Команда зависит от контракта репозитория пользователей,
// а не от конкретной реализации
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {
    }

    /**
     * @throws CommandException
     * @throws ArgumentsException
     */
    public function handle(Arguments $arguments): void
    {
        $username = $arguments->get('login');

// Проверяем, существует ли пользователь в репозитории
        if ($this->userExists($username)) {
// Бросаем исключение, если пользователь уже существует
            throw new CommandException("User already exists: $username");
        }
        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save(new User(
            UUID::random(),
            new Name(
                $arguments->get('firstName'),
                $arguments->get('lastName')),
            $username,
        ));
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