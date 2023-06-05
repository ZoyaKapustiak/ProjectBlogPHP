<?php

namespace ZoiaProjects\ProjectBlog\Blog\Repositories\LikesRepository;

use PDO;
use Psr\Log\LoggerInterface;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\InvalidArgumentException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\LikeAlreadyExists;
use ZoiaProjects\ProjectBlog\Blog\Like;
use ZoiaProjects\ProjectBlog\Blog\LikeComment;
use ZoiaProjects\ProjectBlog\Blog\LikePost;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\LikeNotFoundException;


readonly class SqliteLikesCommentRepository implements LikesCommentRepositoryInterface
{
    public function __construct(
        private PDO             $connection,
        private LoggerInterface $logger
    ){}

    public function save(LikeComment $like): void
    {
        $this->logger->info("Like start create");
            $statement = $this->connection->prepare(
                'INSERT INTO comment_likes (uuid, commentUuid, userUuid) VALUES (:uuid, :commentUuid, :userUuid)');

            $statement->execute([
                ":uuid" => (string)$like->getUuid(),
                ":commentUuid" => (string)$like->getCommentUuid(),
                ":userUuid" => (string)$like->getUserUuid(),
            ]);
            $this->logger->info("Like created:" . $like->getUuid());

    }

    /**
     * @throws InvalidArgumentException
     * @throws LikeNotFoundException
     */
    public function getByCommentUUID(UUID $commentUuid): array
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM comment_likes WHERE commentUuid = :commentUuid"
        );
        $statement->execute([
            ":commentUuid" => (string)$commentUuid,
        ]);

      $result = $statement->fetchAll(PDO::FETCH_ASSOC);

       if(!$result) {
           $this->logger->warning("Cannot get likes: $commentUuid");
           throw new LikeNotFoundException(
               'No likes to comment with uuid = : ' . $commentUuid
           );
       }

       $likes = [];
       foreach ($result as $like) {
           $likes[] = new LikeComment(
               uuid: new UUID($like['uuid']),
               userUuid: new UUID($like['userUuid']),
               commentUuid: new UUID($like['commentUuid']),
           );
       }
       return $likes;
    }
    public function delete(UUID $uuid): void
    {
        // TODO: Implement delete() method.
    }

    /**
     * @throws LikeAlreadyExists
     */
    public function checkUserLikeForCommentExists(string $commentUuid, UUID $userUuid): void
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM comment_likes WHERE  userUuid = :userUuid AND commentUuid = :commentUuid"
        );
        $statement->execute([
            ":commentUuid" => (string)$commentUuid,
            ":userUuid" => (string)$userUuid
        ]);

        $isExisted = $statement->fetch();

        if ($isExisted) {
            throw new LikeAlreadyExists(
                'The users like for this comment already exists'
            );
        }


    }

}