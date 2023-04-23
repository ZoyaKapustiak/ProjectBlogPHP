<?php

require_once __DIR__ . '/vendor/autoload.php';

use ZoiaProjects\ProjectBlog\Person\Name;
use ZoiaProjects\ProjectBlog\Person\Person;
use ZoiaProjects\ProjectBlog\Blog\Post;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\Repositories\InMemoryUsersRepository;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\UserNotFoundException;
use ZoiaProjects\ProjectBlog\Blog\Comment;


$faker = Faker\Factory::create('ru_RU');
//echo $faker->name() . PHP_EOL;

$name1 = new Name($faker->firstName('male'), $faker->lastName('male'));
$newPerson = new Person($name1, new DateTimeImmutable());
$name2 = new Name($faker->firstName('male'), $faker->lastName('male'));
$newPerson2 = new Person($name2, new DateTimeImmutable());

$user1 = new User(1, $newPerson, 'Admin');
$user2 = new User(2, $newPerson2, 'Юзер');
$newPost = new Post($user1,$faker->title(), $faker->sentence(5));

//echo $newPost . PHP_EOL;

$routes = $argv[1] ?? null;
switch ($routes) {
    case 'user':
        echo $user1;
        break;
    case 'post':
        echo $newPost;
        break;
    case 'comment':
        $comment = new Comment($user2, $newPost, $faker->text(5));
        echo $comment;
        break;
    default:
        echo 'Error try User, Post, Comment parametr';
};