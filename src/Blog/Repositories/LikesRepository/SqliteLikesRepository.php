<?php

namespace ZoiaProjects\ProjectBlog\Blog\Repositories\LikesRepository;

use PDO;
use Psr\Log\LoggerInterface;
use ZoiaProjects\ProjectBlog\Blog\Like;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\SqliteUsersRepository;
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
        $this->logger->info("Create like command started");

        $statementUser = $this->connection->prepare(
            'SELECT * FROM likes WHERE userUuid = :userUuid AND postOrCommentUuid = :postOrCommentUuid'
        );
        $statementUser->execute([
            ':postOrCommentUuid' => $like->getPostOrCommentUuid(),
            ':userUuid' => $like->getUserUuid(),
        ]);
        $result = $statementUser->fetch(PDO::FETCH_ASSOC);

        if($result === false) {
            $statement = $this->connection->prepare(
                'INSERT INTO likes (uuid, postOrCommentUuid, userUuid) VALUES (:uuid, :postOrCommentUuid, :userUuid)'
            );
            $statement->execute([
                ":uuid" => $like->getUuid(),
                ":postOrCommentUuid" => $like->getPostOrCommentUuid(),
                ":userUuid" => $like->getUserUuid()
            ]);
        } else {
            echo 'Этот юзер уже поставил свой лайк';
        }
        $this->logger->info("Like created: $like");
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

}