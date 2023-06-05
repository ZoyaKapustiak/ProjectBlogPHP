<?php

use Psr\Log\LoggerInterface;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\AppException;
use ZoiaProjects\ProjectBlog\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\SqliteUsersRepository;
use ZoiaProjects\ProjectBlog\HTTP\Actions\AuthAction\LogIn;
use ZoiaProjects\ProjectBlog\HTTP\Actions\AuthAction\LogOut;
use ZoiaProjects\ProjectBlog\HTTP\Actions\Comments\DeleteComment;
use ZoiaProjects\ProjectBlog\HTTP\Actions\Likes\CreateLikeComment;
use ZoiaProjects\ProjectBlog\HTTP\Actions\Likes\FindByCommentLikes;
use ZoiaProjects\ProjectBlog\HTTP\Actions\Likes\FindByPostLikes;
use ZoiaProjects\ProjectBlog\HTTP\Actions\Users\FindByLogin;
use ZoiaProjects\ProjectBlog\HTTP\Request;
use ZoiaProjects\ProjectBlog\HTTP\SuccessfulResponse;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\HttpException;
use ZoiaProjects\ProjectBlog\HTTP\ErrorResponse;
use ZoiaProjects\ProjectBlog\HTTP\Actions\Users\CreateUser;
use ZoiaProjects\ProjectBlog\HTTP\Actions\Posts\CreatePost;
use ZoiaProjects\ProjectBlog\HTTP\Actions\Comments\CreateComment;
use ZoiaProjects\ProjectBlog\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use ZoiaProjects\ProjectBlog\HTTP\Actions\Posts\DeletePost;
use ZoiaProjects\ProjectBlog\HTTP\Actions\Likes\CreateLikePost;

$container = require __DIR__ . '/bootstrap.php';

$logger = $container->get(LoggerInterface::class);

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

try {
    $path = $request->path();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}
try {
    $method = $request->method();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' => [
        '/user/show' => FindByLogin::class,
        '/post/getLikes' => FindByPostLikes::class,
        '/comment/getLikes' => FindByCommentLikes::class
    ],
    'POST' => [
        '/login' => LogIn::class,
        '/logout' => LogOut::class,
        '/users/create' => CreateUser::class,
        '/post/create' => CreatePost::class,
        '/post/comment' => CreateComment::class,
        '/post/like' => CreateLikePost::class,
        '/comment/like' => CreateLikeComment::class,
    ],
    'DELETE' => [
        '/posts' => DeletePost::class,
        '/comments' => DeleteComment::class,
    ],
];

// Если у нас нет маршрутов для метода запроса -
// возвращаем неуспешный ответ
if (!array_key_exists($method, $routes) || !array_key_exists($path, $routes[$method])) {
    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse("Route not found: $method $path"))->send();
    return;
}


// Выбираем действие по методу и пути
$actionClassName = $routes[$method][$path];
$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);

} catch (Exception $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse($e->getMessage()))->send();
    return;
}
$response->send();
