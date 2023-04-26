<?php

namespace ZoiaProjects\ProjectBlog\Blog;

//use src\Person\Name;

use ZoiaProjects\ProjectBlog\Person\Name;


class User
{

    public function __construct(
        private UUID $uuid,
        private Name $username,
        private string $login
    ) {
    }
    public function __toString(): string
    {
        return 'Юзер с именем: ' . $this->username . ' и логином:' . $this->login . PHP_EOL;
    }

    /**
     * @return int
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

}