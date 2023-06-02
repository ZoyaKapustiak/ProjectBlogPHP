<?php

namespace ZoiaProjects\ProjectBlog\Blog\Repositories\LikesRepository;

use ZoiaProjects\ProjectBlog\Blog\Like;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\UUID;

interface LikesRepositoryInterface
{
    public function save(Like $like): void;

    public function getByPostOrCommentUUID(UUID $postUuid): array;

    public function delete(UUID $uuid): void;

    public function checkUserLikeForPostOrCommentExists(string $postOrCommentUuid, User $user): void;
}