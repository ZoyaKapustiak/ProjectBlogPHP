<?php

namespace ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository;

use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\Person\Name;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\UserNotFoundException;

class DummyUsersRepository implements UsersRepositoryInterface
{

    public function save(User $user): void
    {
        // TODO: Implement save() method.
    }

    public function getByUUID(UUID $uuid): User
    {
        throw new UserNotFoundException("Not found");
    }

    public function getByLogin(string $login): User
    {
        return new User(UUID::random(), new Name("first", "last"), "user123", "123");
    }
}