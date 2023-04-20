<?php

namespace src\Blog;

use src\Person\Name;

class User
{
    private int $id;
    private Name $username;
    private string $login;
    public function __construct(Name $username, string $login)
    {
        $this->username = $username;
        $this->login = $login;
    }
    public function User__toString()
    {
        return $this->username . 'login:' . $this->login;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
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