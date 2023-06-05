<?php

namespace ZoiaProjects\ProjectBlog;

use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\LikeNotFoundException;
use ZoiaProjects\ProjectBlog\Blog\Like;
use ZoiaProjects\ProjectBlog\Blog\Repositories\LikesRepository\SqliteLikesPostPostRepository;
use ZoiaProjects\ProjectBlog\Blog\UUID;

class SqliteLikesRepositoryTest extends TestCase
{

    public function testItSavesLikesToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);
        $statementMock->expects($this->once())->method('execute')->with([
            ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
            ':userUuid' => '123e4567-e89b-12d3-a456-426614174002',
            ':postOrCommentUuid' => '123e4567-e89b-12d3-a456-426614174001'
        ]);
        $connectionStub->method('prepare')->willReturn($statementMock);

        $repositoryPost = new SqliteLikesPostPostRepository($connectionStub, new DummyLogger());
        $like = new Like(
            new UUID('123e4567-e89b-12d3-a456-426614174000'),
            new UUID('123e4567-e89b-12d3-a456-426614174001'),
            new UUID('123e4567-e89b-12d3-a456-426614174002')
        );
        $repositoryPost->save($like);
    }

    /**
     * @throws LikeNotFoundException
     * @throws Exception
     */
    public function testItGetPostByUuidNotFound(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createStub(PDOStatement::class);
        $statementMock->method('fetch')->willReturn([
            'uuid' => '123e4567-e89b-12d3-a456-426614174002',
            'authorUuid' => '123e4567-e89b-12d3-a456-426614174001',
            'headerText' => 'Заголовок',
            'text' => 'Текст теста',
            'login' => 'ivan123',
            'firstName' => 'Ivan',
            'lastName' => 'Nikitin',
            'postUuid' => '123e4567-e89b-12d3-a456-426614174003'
        ]);
        $connectionStub->method('prepare')->willReturn($statementMock);
        $repositoryLike = new SqliteLikesPostPostRepository($connectionStub, new DummyLogger());
        $this->expectException(LikeNotFoundException::class);
        $this->expectExceptionMessage('No likes to post with uuid = : 123e4567-e89b-12d3-a456-426614174000');
        $repositoryLike->getByPostOrCommentUUID(new UUID('123e4567-e89b-12d3-a456-426614174000'));
    }

}