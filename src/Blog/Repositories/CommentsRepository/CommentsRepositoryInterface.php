<?php

namespace ZoiaProjects\ProjectBlog\Blog\Repositories\CommentsRepository;

use ZoiaProjects\ProjectBlog\Blog\Comment;
use ZoiaProjects\ProjectBlog\Blog\UUID;

interface CommentsRepositoryInterface
{
    public function save(Comment $comment): void;

    public function getByUUID(UUID $uuid): Comment;

    public function getByLogin(string $login): Comment;
}