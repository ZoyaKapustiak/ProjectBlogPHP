<?php

namespace ZoiaProjects\ProjectBlog\Blog;

class Comment
{
    public function __construct(
        private UUID $uuid,
        private User $author,
        private Post $post,
        private string $comment,
    ) {

    }
    public function __toString()
    {
        return $this->author . 'прокомментировал пост ' . $this->post->getAuthor() . 'словами: ' . $this->comment . '.' . PHP_EOL;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @param Post $post
     */
    public function setPost(Post $post): void
    {
        $this->post = $post;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return int
     */
    public function uuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

}