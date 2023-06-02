<?php

namespace ZoiaProjects\ProjectBlog\Blog\Repositories\LikesRepository;

use PDO;
use Psr\Log\LoggerInterface;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\LikeAlreadyExists;
use ZoiaProjects\ProjectBlog\Blog\Like;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\SqliteUsersRepository;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\LikeNotFoundException;

class SqliteLikesRepository implements LikesRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    ){}

    public function save(Like $like): void
    {
            $statement = $this->connection->prepare(
                'INSERT INTO likes (uuid, postOrCommentUuid, userUuid) VALUES (:uuid, :postOrCommentUuid, :userUuid)');

            $statement->execute([
                ":uuid" => $like->getUuid(),
                ":postOrCommentUuid" => $like->getPostOrCommentUuid(),
                ":userUuid" => $like->getUserUuid()
            ]);
            $this->logger->info("Like created:" . $like->getUuid());

    }

    public function getByPostOrCommentUUID(UUID $postOrCommentUuid): array
    {
        $statement = $this->connection->prepare(
            "SELECT userUuid FROM likes WHERE postOrCommentUuid = :postOrCommentUuid"
        );
        $statement->execute([
            ":postOrCommentUuid" => $postOrCommentUuid
        ]);

      $result = $statement->fetchAll(PDO::FETCH_ASSOC);

       if(!$result) {
           $this->logger->warning("Cannot get likes: $postOrCommentUuid");
           throw new LikeNotFoundException(
               'No likes to post with uuid = : ' . $postOrCommentUuid
           );
       }
       $likes = [];
       foreach ($result as $like) {
           $likes[] = new Like(
               uuid: new UUID($like['uuid']),
               postOrCommentUuid: new UUID($like['postOrCommentUuid']),
               userUuid: new UUID($like['userUuid']),
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
    public function checkUserLikeForPostOrCommentExists(string $postOrCommentUuid, User $user): void
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM likes WHERE  userUuid = :userUuid AND postOrCommentUuid = :postOrCommentUuid"
        );
        $statement->execute([
            ":postOrCommentUuid" => (string)$postOrCommentUuid,
            ":userUuid" => (string)$user->uuid()
        ]);

        $isExisted = $statement->fetch();

        if ($isExisted) {
            throw new LikeAlreadyExists(
                'The users like for this post already exists'
            );
        }


    }

}