<?php

namespace ZoiaProjects\ProjectBlog\Blog\Repositories\PostsRepository;

use \PDO;
use \PDOStatement;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\PostNotFoundException;
use ZoiaProjects\ProjectBlog\Blog\Post;
use ZoiaProjects\ProjectBlog\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\SqliteUsersRepository;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\UUID;

class SqlitePostsRepository implements PostsRepositoryInterface
{

    public function __construct(
        private PDO $connection,
    ){
    }

    public function save(Post $post): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, authorUuid, headerText, text)
            VALUES (:uuid, :authorUuid, :headerText, :text)'
        );
        $statement->execute([
            ':uuid' => $post->uuid(),
            ':authorUuid' => $post->getAuthor()->uuid(),
            ':headerText' => $post->getHeaderText(),
            ':text' => $post->getText(),
        ]);
    }

    public function getByUUID(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE uuid = :uuid'
        );

        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getPost($statement, $uuid);

//        return new User(new UUID($result['uuid']),
//        new Name($result['firstName'], $result['lastName']), $result['login']);
    }

    private function getPost(\PDOStatement $statement, $postUuid): Post
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new PostNotFoundException(
                "Cannot find post: $postUuid"
            );
        }

        $userRepository = new SqliteUsersRepository($this->connection);
        $user = $userRepository->getByUUID(new UUID($result['authorUuid']));

        return new Post(
            new UUID($result['uuid']),
            $user,
            $result['headerText'],
            $result['text']
        );
    }
    public function delete(UUID $uuid): void
    {
        $statement = $this->connection->prepare(
            'DELETE FROM posts WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => $uuid
        ]);
    }
}