<?php

namespace ZoiaProjects\ProjectBlog\HTTP\Actions\Posts;

use ZoiaProjects\ProjectBlog\Blog\Exceptions\HttpException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\InvalidArgumentException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\UserNotFoundException;
use ZoiaProjects\ProjectBlog\Blog\Post;
use ZoiaProjects\ProjectBlog\Blog\Repositories\PostsRepository\PostsRepositoryInterface;

use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\HTTP\Actions\ActionInterface;
use ZoiaProjects\ProjectBlog\HTTP\Request;
use ZoiaProjects\ProjectBlog\HTTP\Response;
use ZoiaProjects\ProjectBlog\HTTP\SuccessfulResponse;
use ZoiaProjects\ProjectBlog\Person\Name;
use ZoiaProjects\ProjectBlog\HTTP\ErrorResponse;

class CreatePost implements ActionInterface
{
    public function __construct(
        public PostsRepositoryInterface $postsRepository,
        public UsersRepositoryInterface $usersRepository,
    ){}

    public function handle(Request $request): Response
    {
        try {
            $authorUuid = new UUID($request->jsonBodyField("authorUuid"));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
           $user = $this->usersRepository->getByUUID($authorUuid);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $newPostUuid = UUID::random();
            $post = new Post(
                $newPostUuid,
                $user,
                $request->jsonBodyField("headerText"),
                $request->jsonBodyField("text")
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $this->postsRepository->save($post);
        return new SuccessfulResponse([
            "uuid" => (string)$newPostUuid,
        ]);
    }
}