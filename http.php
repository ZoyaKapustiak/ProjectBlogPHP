<?php

use ZoiaProjects\ProjectBlog\Blog\Exceptions\AppException;
use ZoiaProjects\ProjectBlog\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\SqliteUsersRepository;
use ZoiaProjects\ProjectBlog\HTTP\Actions\Comments\DeleteComment;
use ZoiaProjects\ProjectBlog\HTTP\Actions\Likes\FindByPostOrCommentLikes;
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
use ZoiaProjects\ProjectBlog\HTTP\Actions\Likes\CreateLike;

$container = require __DIR__ . '/bootstrap.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}
try {
    $method = $request->method();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' => [
        '/users/show' => FindByLogin::class,
        '/post/getLikes' => FindByPostOrCommentLikes::class
    ],
    'POST' => [
        '/users/create' => CreateUser::class,
        '/posts/create' => CreatePost::class,
        '/posts/comment' => CreateComment::class,
        '/post/like' => CreateLike::class,
        '/comment/like' => CreateLike::class,
    ],
    'DELETE' => [
        '/posts' => DeletePost::class,
        '/comments' => DeleteComment::class,
    ],
];

// Если у нас нет маршрутов для метода запроса -
// возвращаем неуспешный ответ
if (!array_key_exists($method, $routes)) {
    (new ErrorResponse("Route not found: $method $path"))->send();
    return;
}

// Ищем маршрут среди маршрутов для этого метода
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse("Route not found: $method $path"))->send();
    return;
}

// Выбираем действие по методу и пути
$actionClassName = $routes[$method][$path];
$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
    $response->send();
} catch (Exception $e) {
    (new ErrorResponse($e->getMessage()))->send();
}
