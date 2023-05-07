<?php

require_once __DIR__ . '/vendor/autoload.php';

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');


use ZoiaProjects\ProjectBlog\Blog\Comment;
use ZoiaProjects\ProjectBlog\Blog\Post;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\SqliteUsersRepository;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\Person\Name;
use ZoiaProjects\ProjectBlog\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use ZoiaProjects\ProjectBlog\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;


$faker = Faker\Factory::create('ru_RU');


$name1 = new Name($faker->firstName('male'), $faker->lastName('male'));

$user1 = new User(UUID::random(), $name1, 'Admin');

$newPost = new Post(UUID::random(),$user1,$faker->title(), $faker->sentence(5));
$newComment = new Comment(UUID::random(), $user1, $newPost, $faker->sentence);
$userRepository = new SqliteUsersRepository($connection);
$postRepository = new SqlitePostsRepository($connection);
$commentRepository = new SqliteCommentsRepository($connection);

$userRepository->save($user1);
$postRepository->save($newPost);
$commentRepository->save($newComment);

$post = $postRepository->getByUUID(new UUID('b304793a-d48e-44cf-a85b-78a16d1640a7'));
$comment = $commentRepository->getByUUID(new UUID('3e68892b-89bc-4136-a3ce-f6547f4f6b20'));
echo $comment;


