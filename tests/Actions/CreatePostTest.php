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
use ZoiaProjects\ProjectBlog\HTTP\Auth\IdentificationInterface;
use ZoiaProjects\ProjectBlog\HTTP\Auth\JsonBodyLoginIdentification;
use ZoiaProjects\ProjectBlog\HTTP\Auth\JsonBodyUuidIdentification;
use ZoiaProjects\ProjectBlog\HTTP\Request;
use ZoiaProjects\ProjectBlog\HTTP\SuccessfulResponse;
use ZoiaProjects\ProjectBlog\Person\Name;
use ZoiaProjects\ProjectBlog\HTTP\ErrorResponse;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\AuthException;


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
        $postsRepositoryStub = $this->createStub(PostsRepositoryInterface::class);
        $authenticationStub = $this->createStub(JsonBodyLoginIdentification::class);

        $authenticationStub
            ->method('user')
            ->willReturn(
                new User(
                    new UUID("10373537-0805-4d7a-830e-22b481b4859c"),
                    new Name('first', 'last'),
                    'username',
                )
            );

        $createPost = new CreatePost(
            $postsRepositoryStub,
            $authenticationStub,
            new DummyLogger()
        );

        $request = new Request(
            [],
            [],
            '{
                "headerText": "lorem",
                "text": "lorem"
                }'
        );

        $actual = $createPost->handle($request);

        $this->assertInstanceOf(
            SuccessFulResponse::class,
            $actual
        );
    }


    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnErrorIfIncorrectUuid(): void
    {
        $request = new Request(get: [], server: [], body: '{"authorUuid":"error","headerText":"title","text":"text"}');

        $postsRepository = $this->postsRepository([]);
        $identificationStub = $this->createStub(JsonBodyLoginIdentification::class);
        $identificationStub->method('user')->willThrowException(new AuthException('Malformed UUID: error'));

        $action = new CreatePost($postsRepository, $identificationStub, new DummyLogger());

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);

        $this->expectOutputString('{"success":false,"reason":"Malformed UUID: error"}');
        $response->send();
    }

    public function testItReturnErrorIfNotFoundUser(): void
    {
        $request = new Request(get: [], server: [], body: '{"authorUuid":"10373537-0805-4d7a-830e-22b481b485c","headerText":"title","text":"text"}');

        $postsRepository = $this->postsRepository([]);
//        $postsRepositoryStub = $this->createStub(PostsRepositoryInterface::class);
        $authenticationStub = $this->createStub(JsonBodyUuidIdentification::class);
        $authenticationStub
            ->method('user')->willThrowException(new AuthException("Cannot find user: 10373537-0805-4d7a-830e-22b481b4859c"));

        $action = new CreatePost($postsRepository, $authenticationStub, new DummyLogger());

        $response = $action->handle($request);
        $response->send();
        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"Cannot find user: 10373537-0805-4d7a-830e-22b481b4859c"}');
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
        $authenticationStub = $this->createStub(JsonBodyUuidIdentification::class);
        $authenticationStub
            ->method('user')
            ->willReturn(
                new User(
                    new UUID("10373537-0805-4d7a-830e-22b481b4859c"),
                    new Name('Ivan', 'Nikitin'),
                    'login',
                )
            );


        $action = new CreatePost($postsRepository, $authenticationStub, new DummyLogger());

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