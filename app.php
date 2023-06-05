<?php

use ZoiaProjects\ProjectBlog\Blog\Commands\Arguments;
use ZoiaProjects\ProjectBlog\Blog\Commands\CreateUserCommand;
use ZoiaProjects\ProjectBlog\Blog\Comment;
use ZoiaProjects\ProjectBlog\Blog\Like;
use ZoiaProjects\ProjectBlog\Blog\Post;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\SqliteUsersRepository;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\Person\Name;
use ZoiaProjects\ProjectBlog\Blog\Repositories\LikesRepository\SqliteLikesPostRepository;

require_once __DIR__ . '/vendor/autoload.php';
$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');
//


$newLike = new Like(
    UUID::random(),
    new UUID('eb229f0c-9477-407e-80e2-e40ca1908cc2'),
    new UUID('6dab4e05-74ea-48fc-87d2-6dfe18955a52')
);
$likeRepo = new SqliteLikesPostRepository($connection);
$likeRepo->save($newLike);
print_r($likeRepo->getByPostOrCommentUUID(new UUID('eb229f0c-9477-407e-80e2-e40ca1908cc1')));
//$likeRepo->getByPostUUID()
//$faker = Faker\Factory::create('ru_RU');
//
//
//$name1 = new Name($faker->firstName('male'), $faker->lastName());
//
//$user1 = new User(UUID::random(), $name1, 'Admin');
//
//$newPost = new Post(UUID::random(),$user1,$faker->title(), $faker->sentence(5));
//$newComment = new Comment(UUID::random(), $user1, $newPost, $faker->sentence);
//$userRepository = new SqliteUsersRepository($connection);
//
//
//$command = new CreateUserCommand($userRepository);
//
//
//try {
//    $command->handle(Arguments::fromArgv($argv));
//} catch (Exception $e) {
//    echo $e->getMessage();
//}
//$routes = $argv[1] ?? null;
//
//switch ($routes) {
//    case 'user':
//        echo $user1;
//        break;
//    case 'post':
//        echo $newPost;
//        break;
//    case 'comment':
//        $comment = new Comment($faker->randomDigitNotNull(),$user1, $newPost, $faker->text(5));
//        echo $comment;
//        break;
//    default:
//        echo 'Error try user, post, comment parameter';
//};
//
//$userRepository->save($user1);

//
//$routes = $argv[1] ?? null;
//
//switch ($routes) {
//    case 'user':
//        echo $user1;
//        break;
//    case 'post':
//        echo $newPost;
//        break;
//    case 'comment':
//        $comment = new Comment($faker->randomDigitNotNull(),$user1, $newPost, $faker->text(5));
//        echo $comment;
//        break;
//    default:
//        echo 'Error try user, post, comment parameter';
//};