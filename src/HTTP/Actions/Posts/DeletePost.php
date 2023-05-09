<?php

namespace ZoiaProjects\ProjectBlog\HTTP\Actions\Posts;

use ZoiaProjects\ProjectBlog\Blog\Exceptions\HttpException;
use ZoiaProjects\ProjectBlog\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\HTTP\Actions\ActionInterface;
use ZoiaProjects\ProjectBlog\HTTP\ErrorResponse;
use ZoiaProjects\ProjectBlog\HTTP\Request;
use ZoiaProjects\ProjectBlog\HTTP\Response;
use ZoiaProjects\ProjectBlog\HTTP\SuccessfulResponse;

class DeletePost implements ActionInterface
{
    public function __construct(
        public PostsRepositoryInterface $postsRepository
    ){

    }

    public function handle(Request $request): Response
    {
        try {
            $postUuid = $request->query("uuid");
            $this->postsRepository->getByUUID(new UUID($postUuid));
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $this->postsRepository->delete(new UUID($postUuid));

        return new SuccessfulResponse([
            'uuid' => (string)$postUuid
        ]);

    }
}