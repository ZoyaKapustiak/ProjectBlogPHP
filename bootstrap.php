<?php


use ZoiaProjects\ProjectBlog\Blog\Container\DIContainer;
use ZoiaProjects\ProjectBlog\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\SqliteUsersRepository;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use ZoiaProjects\ProjectBlog\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\Repositories\LikesRepository\SqliteLikesRepository;

require_once __DIR__ . '/vendor/autoload.php';

$container = new DIContainer();
$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);
$container->bind(
    PostsRepositoryInterface::class,
    SqlitePostsRepository::class
);
$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);

$container->bind(
    CommentsRepositoryInterface::class,
    SqliteCommentsRepository::class
);
$container->bind(
    LikesRepositoryInterface::class,
    SqliteLikesRepository::class
);
return $container;