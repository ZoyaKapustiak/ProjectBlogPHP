<?php

namespace ZoiaProjects\ProjectBlog\Blog\Repositories;

use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\UserNotFoundException;

class InMemoryUsersRepository
{
    private array $users = [];

    public function saveUser(User $user): void
    {
        $this->users[] = $user;
    }
    public function getUser(int $id): User
    {
        foreach ($this->users as $user) {
            if($user->getId() === $id) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $id");
    }
}