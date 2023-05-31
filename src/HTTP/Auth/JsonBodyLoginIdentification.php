<?php

namespace ZoiaProjects\ProjectBlog\HTTP\Auth;

use ZoiaProjects\ProjectBlog\Blog\Exceptions\AuthException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\HttpException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\UserNotFoundException;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\HTTP\Request;

class JsonBodyLoginIdentification implements IdentificationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    )
    {
    }

    public function user(Request $request): User
    {
        try {
            $login = $request->jsonBodyField('login');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }
        try {
            return $this->usersRepository->getByLogin($login);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
    }
}