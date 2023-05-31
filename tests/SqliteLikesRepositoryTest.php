<?php

namespace ZoiaProjects\ProjectBlog;

use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use ZoiaProjects\ProjectBlog\Blog\Like;
use ZoiaProjects\ProjectBlog\Blog\Repositories\LikesRepository\SqliteLikesRepository;
use ZoiaProjects\ProjectBlog\Blog\UUID;

class SqliteLikesRepositoryTest extends TestCase
{

    public function testItSavesCommentToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock->expects($this->once())->method('execute')->with([
            ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
            ':postUuid' => '123e4567-e89b-12d3-a456-426614174001',
            ':userUuid' => '123e4567-e89b-12d3-a456-426614174002',
        ]);
        $connectionStub->method('prepare')->willReturn($statementMock);

        $repositoryPost = new SqliteLikesRepository($connectionStub, new DummyLogger());
        $repositoryPost->save(new Like(
                new UUID('123e4567-e89b-12d3-a456-426614174000'),
                new UUID('123e4567-e89b-12d3-a456-426614174001'),
                new UUID('123e4567-e89b-12d3-a456-426614174002')
        ));
    }

}