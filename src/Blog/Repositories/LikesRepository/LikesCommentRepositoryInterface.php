<?php

namespace ZoiaProjects\ProjectBlog\Blog\Repositories\LikesRepository;

use ZoiaProjects\ProjectBlog\Blog\Like;
use ZoiaProjects\ProjectBlog\Blog\LikeComment;
use ZoiaProjects\ProjectBlog\Blog\LikePost;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\UUID;

interface LikesCommentRepositoryInterface
{
    public function save(LikeComment $like): void;

    public function getByCommentUUID(UUID $commentUuid): array;

    public function delete(UUID $uuid): void;

    public function checkUserLikeForCommentExists(string $commentUuid, UUID $userUuid): void;
}