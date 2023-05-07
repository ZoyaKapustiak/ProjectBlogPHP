<?php

namespace ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository;

use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\UUID;

interface UsersRepositoryInterface
{
    public function save(User $user): void;
    public function getByUUID(UUID $uuid): User;
    public function getByLogin(string $login): User;
}