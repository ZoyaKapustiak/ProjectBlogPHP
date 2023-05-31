<?php

namespace ZoiaProjects\ProjectBlog;

use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\HTTP\Auth\IdentificationInterface;
use ZoiaProjects\ProjectBlog\HTTP\Request;

class DummyIdentification implements IdentificationInterface
{

    public function user(Request $request): User
    {
        // TODO: Implement user() method.
    }
}