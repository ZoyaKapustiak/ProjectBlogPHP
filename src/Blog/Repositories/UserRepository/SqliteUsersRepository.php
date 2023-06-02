<?php

namespace ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository;
use PDO;
use Psr\Log\LoggerInterface;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\UserNotFoundException;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\Person\Name;
use \PDOStatement;


class SqliteUsersRepository implements UsersRepositoryInterface
{
    public function __construct(
        private PDO $connection,
        private LoggerInterface $logger
    ){
    }
    public function save(User $user): void
    {

        $statement = $this->connection->prepare(
            'INSERT INTO users (firstName, lastName, uuid, login, password)
            VALUES (:firstName, :lastName, :uuid, :login, :password)'
        );

        //            ON CONFLICT (uuid) DO UPDATE SET
//            firstName = :firstName,
//            lastName = :lastName
        $statement->execute([
            ':firstName' => $user->name()->first(),
            ':lastName' => $user->name()->last(),
            ':uuid' => $user->uuid(),
            ':login' => $user->getLogin(),
            ':password' => $user->hashedPassword()
        ]);
        $this->logger->info("User created: " . $user->getLogin());
    }
    public function getByUUID(UUID $uuid): User
    {

        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = ?'
        );


        return $this->getUser($statement, (string)$uuid);
//        return new User(new UUID($result['uuid']),
//        new Name($result['firstName'], $result['lastName']), $result['login']);
    }
    public function getByLogin(string $login): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE login = :login'
        );
        $statement->execute([
            ':login' => $login,
        ]);

        return $this->getUser($statement, $login);
    }
    public function getUser(PDOStatement $statement, $findString): User
    {
        $statement->execute([(string)$findString]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new UserNotFoundException('Cannot get user:' . $findString);
        }
        return new User(
            new UUID((string)$result['uuid']),
            new Name($result['firstName'], $result['lastName']),
            (string)$result['login'],
            (string)$result['password']
        );
    }
}