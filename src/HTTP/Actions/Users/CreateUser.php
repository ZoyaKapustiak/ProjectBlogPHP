<?php

namespace ZoiaProjects\ProjectBlog\HTTP\Actions\Users;

use ZoiaProjects\ProjectBlog\Blog\Exceptions\HttpException;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\HTTP\ErrorResponse;
use ZoiaProjects\ProjectBlog\HTTP\SuccessfulResponse;
use ZoiaProjects\ProjectBlog\Person\Name;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use ZoiaProjects\ProjectBlog\HTTP\Actions\ActionInterface;
use ZoiaProjects\ProjectBlog\HTTP\Request;
use ZoiaProjects\ProjectBlog\HTTP\Response;


class CreateUser implements ActionInterface
{

    public function __construct(
     private readonly UsersRepositoryInterface $usersRepository
    ){
    }

    public function handle(Request $request): Response
    {
        try {
            $user = User::createFrom(
                $request->jsonBodyField('login'),
                new Name(
                    $request->jsonBodyField('firstName'),
                    $request->jsonBodyField('lastName')
                ),
                $request->jsonBodyField('password')
            );

        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());

        }

        $this->usersRepository->save($user);

        return new SuccessfulResponse([
            'uuid' => (string)$user->getLogin(),
        ]);
    }
}