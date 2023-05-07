<?php

namespace ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository;

use ZoiaProjects\ProjectBlog\Blog\Exceptions\UserNotFoundException;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\UsersRepositoryInterface;

class InMemoryUsersRepository implements UsersRepositoryInterface
{
    private array $users = [];

    public function save(User $user): void
    {
        $this->users[] = $user;
    }

    /**
     * @param UUID $uuid
     * @param $uuid
     * @return User
     * @throws UserNotFoundException
     */
    public function getByUUID(UUID $uuid): User
    {
        foreach ($this->users as $user) {
            if((string)$user->getId() === (string)$uuid) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $uuid");

    }

    /**
     * @throws UserNotFoundException
     */
    public function getByLogin(string $login): User
    {
        foreach ($this->users as $user) {
            if((string)$login === (string)$login) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $login");
    }
}