<?php

namespace ZoiaProjects\ProjectBlog\Blog\Repositories\PostsRepository;


use PDO;
use PDOStatement;
use Psr\Log\LoggerInterface;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\InvalidArgumentException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\PostNotFoundException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\PostRepositoryException;
use ZoiaProjects\ProjectBlog\Blog\Post;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\SqliteUsersRepository;
use ZoiaProjects\ProjectBlog\Blog\UUID;

readonly class SqlitePostsRepository implements PostsRepositoryInterface
{

    public function __construct(
        private PDO             $connection,
        private LoggerInterface $logger
    ){
    }

    public function save(Post $post): void
    {
        $this->logger->info("Create post command started");
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
        $this->logger->info("Post created: $post");
    }

    /**
     * @throws PostNotFoundException|InvalidArgumentException
     */
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

    /**
     * @throws InvalidArgumentException
     * @throws PostNotFoundException
     */
    private function getPost(PDOStatement $statement, $postUuid): Post
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            $this->logger->warning("Cannot get post: $postUuid");
            throw new PostNotFoundException(
                "Cannot find post: $postUuid"
            );
        }

        $userRepository = new SqliteUsersRepository($this->connection, $this->logger);
        $user = $userRepository->getByUUID(new UUID($result['authorUuid']));

        return new Post(
            new UUID($result['uuid']),
            $user,
            $result['headerText'],
            $result['text']
        );
    }

    /**
     * @throws PostRepositoryException
     */
    public function delete(UUID $uuid): void
    {
        try {
            $statement = $this->connection->prepare(
                'DELETE FROM posts WHERE uuid = :uuid'
            );
            $statement->execute([
                ':uuid' => (string)$uuid
            ]);
        } catch (\PDOException $e) {
            throw new PostRepositoryException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }

    }
}