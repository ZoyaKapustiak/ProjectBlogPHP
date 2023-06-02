<?php

namespace ZoiaProjects\ProjectBlog\HTTP\Actions\Posts;

use Psr\Log\LoggerInterface;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\AuthException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\HttpException;
use ZoiaProjects\ProjectBlog\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\HTTP\Actions\ActionInterface;
use ZoiaProjects\ProjectBlog\HTTP\Auth\TokenAuthenticationInterface;
use ZoiaProjects\ProjectBlog\HTTP\ErrorResponse;
use ZoiaProjects\ProjectBlog\HTTP\Request;
use ZoiaProjects\ProjectBlog\HTTP\Response;
use ZoiaProjects\ProjectBlog\HTTP\SuccessfulResponse;

class DeletePost implements ActionInterface
{
    public function __construct(
        public PostsRepositoryInterface $postsRepository,
        private readonly TokenAuthenticationInterface $authentication,
        private readonly LoggerInterface         $logger,
    ){

    }

    public function handle(Request $request): Response
    {
        try {
            $user = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
            $postUuid = $request->query("uuid");
            $this->postsRepository->getByUUID(new UUID($postUuid));
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $this->postsRepository->delete(new UUID($postUuid));
        $this->logger->info('Post deleted: ' . $postUuid);

        return new SuccessfulResponse([
            'uuid' => (string)$postUuid
        ]);

    }
}