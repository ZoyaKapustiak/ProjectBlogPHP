<?php

namespace ZoiaProjects\ProjectBlog\Actions;

use PHPUnit\Framework\TestCase;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\UserNotFoundException;
use ZoiaProjects\ProjectBlog\Blog\Post;
use ZoiaProjects\ProjectBlog\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\DummyLogger;
use ZoiaProjects\ProjectBlog\HTTP\Actions\Posts\CreatePost;
use ZoiaProjects\ProjectBlog\HTTP\Request;
use ZoiaProjects\ProjectBlog\HTTP\SuccessfulResponse;
use ZoiaProjects\ProjectBlog\Person\Name;
use ZoiaProjects\ProjectBlog\HTTP\ErrorResponse;


/**
 * @method setOutputCallback(\Closure $param)
 */
class CreatePostTest extends TestCase
{

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnSuccessfulResponse(): void
    {
        $request = new Request(get: [], server: [], body: '{"authorUuid":"10373537-0805-4d7a-830e-22b481b4859c","headerText":"title","text":"text"}');

        // На этот раз в репозитории есть нужный нам пользователь
        $usersRepository = $this->usersRepository([
            new User(
                new UUID("10373537-0805-4d7a-830e-22b481b4859c"),
                new Name('Ivan', 'Nikitin'),
                'login',
            ),
        ]);
        $user = $usersRepository->getByUUID(new UUID('10373537-0805-4d7a-830e-22b481b4859c'));
        $post = new Post(
            new UUID('351739ab-fc33-49ae-a62d-b606b7038c87'),
            $user,
            $request->jsonBodyField("headerText"),
            $request->jsonBodyField("text")
        );
        $postsRepository = $this->postsRepository([]);

        $action = new CreatePost($postsRepository, $usersRepository, new DummyLogger());

        $response = $action->handle($request);

        $postsRepository->save($post);

        // Проверяем, что ответ - удачный
        $this->assertInstanceOf(SuccessfulResponse::class, $response);
        $this->callback(function ($data){             //не работает setOutputCallback!!!!!!
            $dataDecode = json_decode(
                $data,
                associative: true,
                flags: JSON_THROW_ON_ERROR
            );

            $dataDecode['data']['uuid'] = "351739ab-fc33-49ae-a62d-b606b7038c87";
            return json_encode(
                $dataDecode,
                JSON_THROW_ON_ERROR
            );
        });
        $this->expectOutputString('{"success":true,"data":{"uuid":"351739ab-fc33-49ae-a62d-b606b7038c87"}}');

        $response->send();

    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnErrorIfIncorrectUuid(): void
    {
        $request = new Request(get: [], server: [], body: '{"authorUuid":"error","headerText":"title","text":"text"}');

        $usersRepository = $this->usersRepository([]);
        $postsRepository = $this->postsRepository([]);

        $action = new CreatePost($postsRepository, $usersRepository, new DummyLogger());

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"success":false,"reason":"Malformed UUID: error"}');
        $response->send();
    }

    public function testItReturnErrorIfNotFoundUser(): void
    {
        $request = new Request(get: [], server: [], body: '{"authorUuid":"10373537-0805-4d7a-830e-22b481b4859c","headerText":"title","text":"text"}');

        $usersRepository = $this->usersRepository([
            new User(
                new UUID("10373537-0805-4d7a-830e-22b481b11111"),
                new Name('Ivan', 'Nikitin'),
                'login',
            ),
        ]);

        $postsRepository = $this->postsRepository([]);

        $action = new CreatePost($postsRepository, $usersRepository, new DummyLogger());

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"success":false,"reason":"Not found user 10373537-0805-4d7a-830e-22b481b4859c"}');
        $response->send();

    }
    public function testItReturnErrorIfNotHeaderText(): void
    {
        $request = new Request(get: [], server: [], body: '{"authorUuid":"10373537-0805-4d7a-830e-22b481b4859c","headerText":"","text":"text"}');
        $usersRepository = $this->usersRepository([
            new User(
                new UUID("10373537-0805-4d7a-830e-22b481b4859c"),
                new Name('Ivan', 'Nikitin'),
                'login',
            ),
        ]);

        $postsRepository = $this->postsRepository([]);

        $action = new CreatePost($postsRepository, $usersRepository, new DummyLogger());

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"success":false,"reason":"Empty field: headerText"}');
        $response->send();
    }

        private function usersRepository(array $users): UsersRepositoryInterface
    {
        return new class ($users) implements UsersRepositoryInterface {

            public function __construct(
                private array $users
            ){}

            public function save(User $user): void
            {
                $this->users[] = $user;
            }

            public function getByUUID(UUID $uuid): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && (string)$uuid === (string)$user->uuid()) {
                        return $user;
                    }
                }
                throw new UserNotFoundException("Not found user " . $uuid);
            }

            public function getByLogin(string $login): User
            {
                throw new UserNotFoundException("Not found");
            }
        };
    }
    private function postsRepository(array $posts): PostsRepositoryInterface
    {
        return new class($posts) implements PostsRepositoryInterface {

            public function __construct(
                private array $posts,
            ) {}

            public function save(Post $post): void
            {
                $this->posts[] = $post;
            }

            public function getByUUID(UUID $uuid): Post
            {
                foreach ($this->posts as $post) {
                    if ($post instanceof Post && (string)$uuid === (string)$post->uuid()) {
                        return $post;
                    }
                }
                throw new UserNotFoundException("Not found post" . $uuid);
            }

            public function delete(UUID $uuid): void
            {
                // TODO: Implement delete() method.
            }
        };
    }

}