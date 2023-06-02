<?php

namespace ZoiaProjects\ProjectBlog\Blog\Repositories\CommentsRepository;

use PDO;
use Psr\Log\LoggerInterface;
use ZoiaProjects\ProjectBlog\Blog\Comment;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\CommentNotFoundException;
use ZoiaProjects\ProjectBlog\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\SqliteUsersRepository;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\UUID;

class SqliteCommentsRepository implements CommentsRepositoryInterface
{

    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    ){
    }
    public function save(Comment $comment): void
    {
        $this->logger->info("Create comment command started");
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, authorUuid, postUuid, comment)
            VALUES (:uuid, :authorUuid, :postUuid, :comment)'
        );
        $statement->execute([
            ':uuid' => $comment->uuid(),
            ':authorUuid' => $comment->getAuthor()->uuid(),
            ':postUuid' => $comment->getPost()->uuid(),
            ':comment' => $comment->getComment()
        ]);
        $this->logger->info("Comment created: " . $comment->uuid());
    }

    public function getByUUID(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = :uuid '
        );
        $statement->execute([
            ':uuid' => $uuid,
        ]);

        return $this->getPost($statement, $uuid);
    }

    public function getByLogin(string $login): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE login = :login '
        );
        $statement->execute([
            ':login' => $login,
        ]);

        return $this->getPost($statement, $login);
    }

    public function getPost(\PDOStatement $statement, $commentUuid): Comment
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($result === false) {
            $this->logger->warning("Cannot get comment: $commentUuid");
            throw new CommentNotFoundException(
                "Cannot find comment: $commentUuid"
            );
        }

        $userRepository = new SqliteUsersRepository($this->connection, $this->logger);
        $user = $userRepository->getByUUID(new UUID($result['authorUuid']));
        $postRepository = new SqlitePostsRepository($this->connection, $this->logger);
        $post = $postRepository->getByUUID(new UUID($result['postUuid']));

        return new Comment(
            new UUID($result['uuid']),
            $user,
            $post,
            $result['comment']
        );
    }
    public function delete(UUID $uuid): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM comments WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => $uuid
        ]);
    }
}