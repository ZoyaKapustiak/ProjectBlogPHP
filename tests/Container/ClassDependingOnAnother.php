<?php

namespace ZoiaProjects\ProjectBlog\Container;

use PHPUnit\Framework\TestCase;

class ClassDependingOnAnother extends TestCase
{
    public function __construct(
        private SomeClassWithoutDependencies $one,
        private SomeClassWithParameter $two,
    ) {}
}