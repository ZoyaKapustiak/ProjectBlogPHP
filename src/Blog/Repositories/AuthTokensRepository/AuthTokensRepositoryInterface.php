<?php

namespace ZoiaProjects\ProjectBlog\Blog\Repositories\AuthTokensRepository;

use ZoiaProjects\ProjectBlog\Blog\AuthToken;

interface AuthTokensRepositoryInterface
{
    public function save(AuthToken $authToken): void;

    public function get(string $token): AuthToken;
}