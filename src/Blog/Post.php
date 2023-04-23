<?php

namespace ZoiaProjects\ProjectBlog\Blog;

class Post
{
    private int $id;
    private int $idAuthor;
    public function __construct(
        private User $author,
        private string $headerText,
        private string $text,
    ) {

    }
    public function __toString(): string
    {
        return $this->author . ' пишет: ' . $this->text;
    }

    /**
     * @return int
     */
    public function getIdAuthor(): int
    {
        return $this->idAuthor;
    }

    /**
     * @param int $idAuthor
     */
    public function setIdAuthor(int $idAuthor): void
    {
        $this->idAuthor = $idAuthor;
    }

    /**
     * @return string
     */
    public function getHeaderText(): string
    {
        return $this->headerText;
    }

    /**
     * @param string $headerText
     */
    public function setHeaderText(string $headerText): void
    {
        $this->headerText = $headerText;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}