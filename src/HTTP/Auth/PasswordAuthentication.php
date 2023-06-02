<?php

namespace ZoiaProjects\ProjectBlog\HTTP\Auth;

use ZoiaProjects\ProjectBlog\Blog\Exceptions\AuthException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\HttpException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\UserNotFoundException;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\HTTP\Request;

class PasswordAuthentication implements PasswordAuthenticationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ){

    }

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        try {
            $login = $request->jsonBodyField('login');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }
        try {
            $user = $this->usersRepository->getByLogin($login);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }
        try {
            $password = $request->jsonBodyField('password');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }

        if (!$user->checkPassword($password)) {
            throw new AuthException('Wrong password');
        }

        return $user;
    }
}