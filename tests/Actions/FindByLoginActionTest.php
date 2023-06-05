<?php

namespace ZoiaProjects\ProjectBlog\Actions;

use PHPUnit\Framework\TestCase;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\UserNotFoundException;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\HTTP\Actions\Users\FindByLogin;
use ZoiaProjects\ProjectBlog\HTTP\ErrorResponse;
use ZoiaProjects\ProjectBlog\HTTP\Request;
use ZoiaProjects\ProjectBlog\HTTP\SuccessfulResponse;
use ZoiaProjects\ProjectBlog\Person\Name;

class FindByLoginActionTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws \JsonException
     */
    // Тест, проверяющий, что будет возвращён неудачный ответ,
    // если в запросе нет параметра login
    public function testItReturnsErrorResponseIfNoLoginProvided(): void
    {
        $request = new Request([], [], "");
        $usersRepository = $this->usersRepository([]);

        $action = new FindByLogin($usersRepository);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"success":false,"reason":"No such query param in the request: login"}');

        $response->send();

    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    // Тест, проверяющий, что будет возвращён неудачный ответ,
    // если пользователь не найден
    public function testItReturnsErrorResponseIfUserNotFound(): void
    {
    // Теперь запрос будет иметь параметр username
        $request = new Request(['login' => 'ivan'], [], '');
    // Репозиторий пользователей по-прежнему пуст
        $usersRepository = $this->usersRepository([]);
        $action = new FindByLogin($usersRepository);
        $response = $action->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"Not found"}');
        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws \JsonException
     */
    // Тест, проверяющий, что будет возвращён удачный ответ,
    // если пользователь найден
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request(['login' => 'ivan'], [], '');
    // На этот раз в репозитории есть нужный нам пользователь
        $usersRepository = $this->usersRepository([
            new User(
                UUID::random(),
                new Name('Ivan', 'Nikitin'),
                'ivan',
                '123'
            ),
        ]);
        $action = new FindByLogin($usersRepository);
        $response = $action->handle($request);
    // Проверяем, что ответ - удачный
        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->expectOutputString('{"success":true,"data":{"login":"ivan","name":"Ivan Nikitin"}}');
        $response->send();
    }


    private function usersRepository(array $users): UsersRepositoryInterface
    {
        return new class($users) implements UsersRepositoryInterface {

            public function __construct(
                private readonly array $users
            ) {
            }

            public function save(User $user): void
            {
                // TODO: Implement save() method.
            }

            public function getByUUID(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByLogin(string $login): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && $login === $user->getLogin()) {
                        return $user;
                    }
                }
                throw new UserNotFoundException("Not found");
            }
        };
    }

}