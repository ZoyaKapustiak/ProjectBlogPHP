<?php

namespace ZoiaProjects\ProjectBlog\HTTP\Auth;

use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\HTTP\Request;

interface IdentificationInterface
{
    public function user(Request $request): User;
}