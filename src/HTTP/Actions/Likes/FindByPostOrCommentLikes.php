<?php

namespace ZoiaProjects\ProjectBlog\HTTP\Actions\Likes;

use ZoiaProjects\ProjectBlog\Blog\Exceptions\HttpException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\InvalidArgumentException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\LikeNotFoundException;
use ZoiaProjects\ProjectBlog\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\HTTP\Actions\ActionInterface;
use ZoiaProjects\ProjectBlog\HTTP\ErrorResponse;
use ZoiaProjects\ProjectBlog\HTTP\Request;
use ZoiaProjects\ProjectBlog\HTTP\Response;
use ZoiaProjects\ProjectBlog\HTTP\SuccessfulResponse;

class FindByPostOrCommentLikes implements ActionInterface
{
    public function __construct(
        public LikesRepositoryInterface $likesRepository,
    ) {}

    public function handle(Request $request): Response
    {
        try {
            $postOrCommentUuid = $request->query("postOrCommentUuid");
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
            $postLikes = $this->likesRepository->getByPostOrCommentUUID(new UUID($postOrCommentUuid));
        } catch (LikeNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }
        return new SuccessfulResponse([
            'postOrCommentUuid' => $postLikes
        ]);

    }
}