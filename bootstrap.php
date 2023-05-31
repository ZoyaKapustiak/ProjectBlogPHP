<?php


use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use ZoiaProjects\ProjectBlog\Blog\Container\DIContainer;
use ZoiaProjects\ProjectBlog\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\SqliteUsersRepository;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use ZoiaProjects\ProjectBlog\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\Repositories\LikesRepository\SqliteLikesRepository;
use Dotenv\Dotenv;
use ZoiaProjects\ProjectBlog\HTTP\Auth\IdentificationInterface;
use ZoiaProjects\ProjectBlog\HTTP\Auth\JsonBodyLoginIdentification;
use ZoiaProjects\ProjectBlog\HTTP\Auth\JsonBodyUuidIdentification;

require_once __DIR__ . '/vendor/autoload.php';

// Загружаем переменные окружения из файла .env
Dotenv::createImmutable(__DIR__)->safeLoad();

$container = new DIContainer();
$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/' . $_ENV['SQLITE_DB_PATH'])
);

$container->bind(
    IdentificationInterface::class,
    JsonBodyLoginIdentification::class
);

$logger = (new Logger('blog'));
if ('yes' === $_ENV['LOG_TO_FILES']) {
    $logger->pushHandler(new StreamHandler(
        __DIR__ . '/logs/blog.log'
    ))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.error.log',
            level: Logger::ERROR,bubble: false,
        ));
}
if ('yes' === $_ENV['LOG_TO_CONSOLE']) {
    $logger->pushHandler(
        new StreamHandler("php://stdout")
    );
}
$container->bind(LoggerInterface::class,
    $logger
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