<?php

namespace src\Blog;

use src\Person\Person;

class Comment
{
    private int $id;

    public function __construct(
        private Person $author,
        private Post $post,
        private string $comment,
    ) {

    }

}