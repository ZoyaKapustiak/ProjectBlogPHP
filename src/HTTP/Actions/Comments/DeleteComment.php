<?php

namespace ZoiaProjects\ProjectBlog\HTTP\Actions\Comments;

use ZoiaProjects\ProjectBlog\Blog\Exceptions\HttpException;
use ZoiaProjects\ProjectBlog\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\HTTP\Actions\ActionInterface;
use ZoiaProjects\ProjectBlog\HTTP\ErrorResponse;
use ZoiaProjects\ProjectBlog\HTTP\Request;
use ZoiaProjects\ProjectBlog\HTTP\Response;
use ZoiaProjects\ProjectBlog\HTTP\SuccessfulResponse;

class DeleteComment implements ActionInterface
{
    public function __construct(
        public CommentsRepositoryInterface $commentsRepository,
    ){}

    public function handle(Request $request): Response
    {
        try {
            $commentUuid = $request->query("uuid");
            $this->commentsRepository->getByUUID(new UUID($commentUuid));
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $this->commentsRepository->delete(new UUID($commentUuid));

        return new SuccessfulResponse([
            'uuid' => $commentUuid,
        ]);
    }
}