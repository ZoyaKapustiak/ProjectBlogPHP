<?php

namespace ZoiaProjects\ProjectBlog\HTTP\Actions\Likes;

use Psr\Log\LoggerInterface;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\AuthException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\HttpException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\InvalidArgumentException;

use ZoiaProjects\ProjectBlog\Blog\LikeComment;
use ZoiaProjects\ProjectBlog\Blog\LikePost;
use ZoiaProjects\ProjectBlog\Blog\Repositories\LikesRepository\LikesCommentRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\Repositories\LikesRepository\LikesPostRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\HTTP\Actions\ActionInterface;
use ZoiaProjects\ProjectBlog\HTTP\Auth\TokenAuthenticationInterface;
use ZoiaProjects\ProjectBlog\HTTP\ErrorResponse;
use ZoiaProjects\ProjectBlog\HTTP\Request;
use ZoiaProjects\ProjectBlog\HTTP\Response;
use ZoiaProjects\ProjectBlog\HTTP\SuccessfulResponse;

class CreateLikeComment implements ActionInterface
{
    public function __construct(
        public LikesCommentRepositoryInterface           $likesRepository,
        private readonly TokenAuthenticationInterface $authentication,

    ) {}

    public function handle(Request $request): Response
    {
        try {
            $user = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $newUuid = UUID::random();
        try {
            $commentUuid = $request->jsonBodyField("commentUuid");
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $this->likesRepository->checkUserLikeForCommentExists($commentUuid, $user->uuid());

        try {
            $like = new LikeComment($newUuid, $user->uuid(), new UUID($commentUuid));
        } catch (InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->likesRepository->save($like);

        return new SuccessfulResponse([
            'uuid' => (string)$newUuid
        ]);
    }
}