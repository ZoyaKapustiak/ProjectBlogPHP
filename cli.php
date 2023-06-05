<?php

use Psr\Log\LoggerInterface;

//require_once __DIR__ . '/vendor/autoload.php';

$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');


use Symfony\Component\Console\Application;
use ZoiaProjects\ProjectBlog\Blog\Commands\FakeData\PopulateDB;
use ZoiaProjects\ProjectBlog\Blog\Commands\Posts\DeletePost;
use ZoiaProjects\ProjectBlog\Blog\Commands\Users\CreateUser;
use ZoiaProjects\ProjectBlog\Blog\Commands\Users\UpdateUser;

$container = require __DIR__ . '/bootstrap.php';
$logger = $container->get(LoggerInterface::class);


// Создаём объект приложения
$application = new Application();

// Перечисляем классы команд
$commandsClasses = [
    CreateUser::class,
    DeletePost::class,
    UpdateUser::class,
    PopulateDB::class
];

foreach ($commandsClasses as $commandClass) {
// Посредством контейнера
// создаём объект команды
    $command = $container->get($commandClass);
// Добавляем команду к приложению
    $application->add($command);
}

try {

    $application->run();

} catch (Exception $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    echo $e->getMessage();
}
