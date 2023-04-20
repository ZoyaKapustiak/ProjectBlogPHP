<?php

namespace src\Blog;

use src\Person\Person;

class Post
{
    private int $id;
    private int $idAuthor;
    public function __construct(
        private Person $author,
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
     * @return Person
     */
    public function getAuthor(): Person
    {
        return $this->author;
    }

    /**
     * @param Person $author
     */
    public function setAuthor(Person $author): void
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