<?php

namespace ZoiaProjects\ProjectBlog\Blog\Repositories\PostsRepository;

use ZoiaProjects\ProjectBlog\Blog\Post;
use ZoiaProjects\ProjectBlog\Blog\UUID;

interface PostsRepositoryInterface
{
    public function save(Post $post): void;
    public function getByUUID(UUID $uuid): Post;
    public function delete(UUID $uuid): void;
}