<?php

namespace ZoiaProjects\ProjectBlog\Blog\Repositories\AuthTokensRepository;

use DateTimeImmutable;
use Exception;
use PDO;
use PDOException;
use ZoiaProjects\ProjectBlog\Blog\AuthToken;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\AuthTokenNotFoundException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\AuthTokensRepositoryException;
use ZoiaProjects\ProjectBlog\Blog\UUID;

class SqliteAuthTokensRepository implements AuthTokensRepositoryInterface
{
    public function __construct(
        private PDO $connection
    )
    {}

    /**
     * @throws AuthTokensRepositoryException
     */
    public function save(AuthToken $authToken): void
    {
        $query = <<<SQL
    INSERT INTO tokens (token, userUuid, expiresOn) VALUES (:token, :userUuid, :expiresOn)
    ON CONFLICT (token) DO UPDATE SET expiresOn = :expiresOn
SQL;
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute([
                ':token' => $authToken->getToken(),
                ':userUuid' => $authToken->getUserUuid(),
                ':expiresOn' => $authToken->getExpiresOn()->format(\DateTimeImmutable::ATOM),
            ]);
        } catch (\PDOException $e) {
            throw new AuthTokensRepositoryException($e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * @throws AuthTokensRepositoryException
     * @throws AuthTokenNotFoundException
     */
    public function get(string $token): AuthToken
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT * FROM tokens WHERE token = ?'
            );
            $statement->execute([$token]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(), (int)$e->getCode(), $e
            );
        }
        if (false === $result) {
            throw new AuthTokenNotFoundException("Cannot find token: $token");
        }
        try {
            return new AuthToken(
                $result['token'],
                new UUID($result['userUuid']),
                new DateTimeImmutable($result['expiresOn'])
            );
        } catch (Exception $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(), $e->getCode(), $e
            );
        }

    }
}