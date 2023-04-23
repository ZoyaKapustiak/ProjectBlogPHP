<?php

use Project2\Blog\Post;
use Project2\Person\Name;
use Project2\Person\Person;

spl_autoload_register(function ($class) {

    $file = str_replace('\\', '/', $class) . '.php';
    $fileProj = str_replace('Project2', 'src', $file);
    if (file_exists($fileProj)) {
        require $fileProj;
    }
});


$name1 = new Name('Иван', 'Иванов');
$newPerson = new Person($name1, new DateTimeImmutable());
$newPost = new Post($newPerson, 'Hello World');
//$user1 = new User($name1, 'admin');

echo $newPost;

