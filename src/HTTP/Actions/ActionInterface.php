<?php

namespace ZoiaProjects\ProjectBlog\HTTP\Actions;

use ZoiaProjects\ProjectBlog\HTTP\Request;
use ZoiaProjects\ProjectBlog\HTTP\Response;

interface ActionInterface
{
    public function handle(Request $request): Response;
}