<?php

namespace ZoiaProjects\ProjectBlog\Blog;

//use src\Person\Name;

use ZoiaProjects\ProjectBlog\Person\Person;

class User
{

    public function __construct(
        private int $id,
        private Person $username,
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
    public function id(): int
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

    /**
     * @return Name
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param Name $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

}