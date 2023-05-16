<?php

namespace ZoiaProjects\ProjectBlog\HTTP\Actions\Likes;

use ZoiaProjects\ProjectBlog\Blog\Exceptions\HttpException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\InvalidArgumentException;

use ZoiaProjects\ProjectBlog\Blog\Like;
use ZoiaProjects\ProjectBlog\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\HTTP\Actions\ActionInterface;
use ZoiaProjects\ProjectBlog\HTTP\ErrorResponse;
use ZoiaProjects\ProjectBlog\HTTP\Request;
use ZoiaProjects\ProjectBlog\HTTP\Response;
use ZoiaProjects\ProjectBlog\HTTP\SuccessfulResponse;

class CreateLike implements ActionInterface
{
    public function __construct(
        public LikesRepositoryInterface $likesRepository,
    ) {}

    public function handle(Request $request): Response
    {
        $newUuid = UUID::random();
        try {
            $postUuid = $request->jsonBodyField("postOrCommentUuid");
            $userUuid = $request->jsonBodyField("userUuid");
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
            $like = new Like($newUuid, new UUID($postUuid), new UUID($userUuid));
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->likesRepository->save($like);

        return new SuccessfulResponse([
            'uuid' => $newUuid
        ]);
    }
}