<?php

namespace ZoiaProjects\ProjectBlog\HTTP\Actions\Comments;

use Psr\Log\LoggerInterface;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\AuthException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\HttpException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\InvalidArgumentException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\UserNoPermission;
use ZoiaProjects\ProjectBlog\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\HTTP\Actions\ActionInterface;
use ZoiaProjects\ProjectBlog\HTTP\Auth\TokenAuthenticationInterface;
use ZoiaProjects\ProjectBlog\HTTP\ErrorResponse;
use ZoiaProjects\ProjectBlog\HTTP\Request;
use ZoiaProjects\ProjectBlog\HTTP\Response;
use ZoiaProjects\ProjectBlog\HTTP\SuccessfulResponse;

class DeleteComment implements ActionInterface
{
    public function __construct(
        public CommentsRepositoryInterface $commentsRepository,
        private readonly TokenAuthenticationInterface $authentication,
        private readonly LoggerInterface         $logger,
    ){}

    /**
     * @throws InvalidArgumentException
     * @throws UserNoPermission
     */
    public function handle(Request $request): Response
    {
        try {
            $user = $this->authentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
            $commentUuid = $request->query("uuid");
            $comment = $this->commentsRepository->getByUUID(new UUID($commentUuid));
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        if((string)$comment->getAuthor()->uuid() !== (string)$user->uuid()) {
            throw new UserNoPermission(
                "User: " . $user->getLogin() . " does not have permission comment user:" . $comment->getAuthor()->getLogin());
        }
        $this->commentsRepository->delete(new UUID($commentUuid));
        $this->logger->info('Comment deleted: ' . $commentUuid);
        return new SuccessfulResponse([
            'uuid' => $commentUuid,
        ]);
    }
}