<?php

namespace ZoiaProjects\ProjectBlog\HTTP\Actions\Likes;

use ZoiaProjects\ProjectBlog\Blog\Exceptions\HttpException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\InvalidArgumentException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\LikeNotFoundException;
use ZoiaProjects\ProjectBlog\Blog\Repositories\LikesRepository\LikesPostRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\HTTP\Actions\ActionInterface;
use ZoiaProjects\ProjectBlog\HTTP\ErrorResponse;
use ZoiaProjects\ProjectBlog\HTTP\Request;
use ZoiaProjects\ProjectBlog\HTTP\Response;
use ZoiaProjects\ProjectBlog\HTTP\SuccessfulResponse;

class FindByPostLikes implements ActionInterface
{
    public function __construct(
        public LikesPostRepositoryInterface $likesRepository,
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $postUuid = $request->query("postUuid");
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
            $postLikes = $this->likesRepository->getByPostUUID(new UUID($postUuid));
        } catch (LikeNotFoundException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        return new SuccessfulResponse([
            'postUuid' => count($postLikes)
        ]);

    }
}