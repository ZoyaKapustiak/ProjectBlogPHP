<?php

namespace ZoiaProjects\ProjectBlog\Blog\Repositories\LikesRepository;

use PDO;
use ZoiaProjects\ProjectBlog\Blog\Like;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\SqliteUsersRepository;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\LikeNotFoundException;

class SqliteLikesRepository implements LikesRepositoryInterface
{
    public function __construct(
        private PDO $connection,
    ){}

    public function save(Like $like): void
    {
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
    }

    public function getByPostOrCommentUUID(UUID $postOrCommentUuid): array
    {
        $statement = $this->connection->prepare(
            "SELECT uuid FROM likes WHERE postOrCommentUuid = :postOrCommentUuid"
        );
        $statement->execute([
            ":postOrCommentUuid" => $postOrCommentUuid
        ]);

       return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    public function delete(UUID $uuid): void
    {
        // TODO: Implement delete() method.
    }

}