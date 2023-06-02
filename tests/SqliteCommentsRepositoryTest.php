<?php

namespace ZoiaProjects\ProjectBlog;

use PHPUnit\Framework\TestCase;
use ZoiaProjects\ProjectBlog\Blog\Comment;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\CommentNotFoundException;
use ZoiaProjects\ProjectBlog\Blog\Post;
use ZoiaProjects\ProjectBlog\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\Person\Name;
use ZoiaProjects\ProjectBlog\DummyLogger;

class SqliteCommentsRepositoryTest extends TestCase
{
    public function testItSavesCommentToDatabase(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);
        $statementMock->expects($this->once())->method('execute')->with([
            ':uuid' => '123e4567-e89b-12d3-a456-426614174000',
            ':authorUuid' => '123e4567-e89b-12d3-a456-426614174001',
            ':postUuid' => '123e4567-e89b-12d3-a456-426614174002',
            ':comment' => 'Комментарий теста'
        ]);
        $connectionStub->method('prepare')->willReturn($statementMock);

        $author = new User(
            new UUID('123e4567-e89b-12d3-a456-426614174001'),
            new Name('Zoia', 'Kapustiak'),
            'admin'
        );
        $post = new Post(
            new UUID('123e4567-e89b-12d3-a456-426614174002'),
            $author,
            'Заголовок теста',
            'Текст теста'
        );

        $repositoryComment = new SqliteCommentsRepository($connectionStub, new DummyLogger());
        $repositoryComment->save(new Comment(
            new UUID('123e4567-e89b-12d3-a456-426614174000'),
            $author,
            $post,
            'Комментарий теста'
        ));
    }
    public function testItGetCommentByUuid(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);
        $statementMock->method('fetch')->willReturn([
            'uuid' => '123e4567-e89b-12d3-a456-426614174000',
            'authorUuid' => '123e4567-e89b-12d3-a456-426614174001',
            'postUuid' => '123e4567-e89b-12d3-a456-426614174002',
            'comment' => 'Комментарий теста',
            'login' => 'admin',
            'firstName' => 'Zoia',
            'lastName' => 'Kapustiak',
            'headerText' => 'Заголовок',
            'text' => 'Текст'
        ]);
        $connectionStub->method('prepare')->willReturn($statementMock);
        $repositoryComment = new SqliteCommentsRepository($connectionStub, new DummyLogger());
        $comment = $repositoryComment->getByUUID(new UUID('123e4567-e89b-12d3-a456-426614174000'));

        $this->assertSame('123e4567-e89b-12d3-a456-426614174000', (string)$comment->uuid());

    }
    public function testItThrowsAnExceptionWhenCommentNotFound(): void
    {
        $connectionStub = $this->createStub(\PDO::class);
        $statementMock = $this->createMock(\PDOStatement::class);
        $statementMock->method('fetch')->willReturn(false);
        $connectionStub->method('prepare')->willReturn($statementMock);
        $repositoryComment = new SqliteCommentsRepository($connectionStub, new DummyLogger());
        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage('Cannot find comment: 123e4567-e89b-12d3-a456-426614174000');
        $repositoryComment->getByUUID(new UUID('123e4567-e89b-12d3-a456-426614174000'));
    }
}