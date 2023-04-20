<?php
use src\Blog\Post;
use src\Person\Name;
use src\Person\Person;



$name1 = new Name('Иван', 'Иванов');
$newPerson = new Person($name1, new DateTimeImmutable());
$newPost = new Post($newPerson,'Приветсвие', 'Hello World');
//$user1 = new User($name1, 'admin');

echo $newPost;