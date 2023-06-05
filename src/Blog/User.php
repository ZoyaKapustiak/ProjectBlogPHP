<?php

namespace ZoiaProjects\ProjectBlog\Blog;

//use src\Person\Name;

use ZoiaProjects\ProjectBlog\Person\Name;
use ZoiaProjects\ProjectBlog\Blog\UUID;


class User extends UUID
{

    public function __construct(
        private readonly UUID $uuid,
        private Name          $username,
        private string        $login,
        private string        $hashedPassword,
    ) {
    }
    private static function hash(string $password, UUID $uuid): string
    {
        return hash('sha256', $uuid . $password);
    }
    public function checkPassword(string $password): string
    {
        return $this->hashedPassword === self::hash($password, $this->uuid);
    }
    public function __toString(): string
    {
        return 'Юзер с именем: ' . $this->username . ' и логином:' . $this->login . PHP_EOL;
    }
    public static function createFrom(
        string $login,
        Name $name,
        string $password,
    ): self
    {
        $uuid = UUID::random();
        return new self(
            $uuid,
            $name,
            $login,
            self::hash($password, $uuid),
        );
    }

    /**
     * @return UUID
     */
    public function uuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return Name
     */
    public function name(): Name
    {
        return $this->username;
    }

    /**
     * @param Name $username
     */
    public function setUsername(Name $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @param string $login
     */
    public function setLogin($login): void
    {
        $this->login = $login;
    }

    /**
     * @return string
     */
    public function hashedPassword(): string
    {
        return $this->hashedPassword;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->hashedPassword = $password;
    }

}