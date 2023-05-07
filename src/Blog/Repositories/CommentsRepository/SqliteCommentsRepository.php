<?php

namespace ZoiaProjects\ProjectBlog\Blog\Repositories\CommentsRepository;

use PDO;
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
    ){

    }

    public function save(Comment $comment): void
    {
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
        // TODO: Implement getByLogin() method.
    }

    public function getPost(\PDOStatement $statement, $commentUuid): Comment
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new CommentNotFoundException(
                "Cannot find post: $commentUuid"
            );
        }
    print_r($result);
        $userRepository = new SqliteUsersRepository($this->connection);
        $user = $userRepository->getByUUID(new UUID($result['authorUuid']));
        $postRepository = new SqlitePostsRepository($this->connection);
        $post = $postRepository->getByUUID(new UUID($result['postUuid']));

        return new Comment(
            new UUID($result['uuid']),
            $user,
            $post,
            $result['comment']
        );
    }
}