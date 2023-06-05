<?php

namespace ZoiaProjects\ProjectBlog\Blog\Repositories\LikesRepository;

use ZoiaProjects\ProjectBlog\Blog\Like;
use ZoiaProjects\ProjectBlog\Blog\LikeComment;
use ZoiaProjects\ProjectBlog\Blog\LikePost;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\UUID;

interface LikesPostRepositoryInterface
{
    public function save(LikePost $like): void;

    public function getByPostUUID(UUID $postUuid): array;

    public function delete(UUID $uuid): void;

    public function checkUserLikeForPostExists(string $postUuid, UUID $userUuid): void;
}