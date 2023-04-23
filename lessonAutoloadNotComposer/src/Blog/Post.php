<?php

namespace Project2\Blog;

use Project2\Person\Person;

class Post
{
    public function __construct(
        private Person $author,
        private string $text,
    ) {

    }
    public function __toString(): string
    {
        return $this->author . ' пишет: ' . $this->text;
    }
}