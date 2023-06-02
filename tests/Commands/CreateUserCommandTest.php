<?php

namespace ZoiaProjects\ProjectBlog\Commands;

use PHPUnit\Framework\TestCase;
use ZoiaProjects\ProjectBlog\Blog\Commands\CreateUserCommand;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\ArgumentsException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\UserNotFoundException;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\DummyUsersRepository;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\CommandException;
use ZoiaProjects\ProjectBlog\Blog\Commands\Arguments;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\Blog\UUID;
use ZoiaProjects\ProjectBlog\DummyLogger;

class CreateUserCommandTest extends TestCase
{
    public function testItRequiresPassword(): void
    {
        $command = new CreateUserCommand(
            $this->makeUsersRepository(),
            new DummyLogger()
        );
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: password');
        $command->handle(new Arguments([
            'login' => 'Ivan',
        ]));
    }

        public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
        // Создаём объект команды
        // У команды одна зависимость - UsersRepositoryInterface
        $command = new CreateUserCommand(new DummyUsersRepository(), new DummyLogger());
        // Описываем тип ожидаемого исключения
        $this->expectException(CommandException::class);
        // и его сообщение
        $this->expectExceptionMessage('User already exists: Ivan');
        // Запускаем команду с аргументами
        $command->handle(new Arguments([
            'login' => 'Ivan',
            'password' => '123'
            ]));
    }


    // Функция возвращает объект типа UsersRepositoryInterface
    private function makeUsersRepository(): UsersRepositoryInterface
    {
        return new class implements UsersRepositoryInterface {
            public function save(User $user): void
            {
            }

            public function getByUUID(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByLogin(string $login): User
            {
                throw new UserNotFoundException("Not found");
            }
        };
    }

    // Тест проверяет, что команда действительно требует фамилию пользователя
    public function testItRequiresLastName(): void
    {
    // Передаём в конструктор команды объект, возвращаемый нашей функцией
        $command = new CreateUserCommand($this->makeUsersRepository(), new DummyLogger());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: lastName');
        $command->handle(new Arguments([
            'login' => 'Ivan',
        // Нам нужно передать имя пользователя,
        // чтобы дойти до проверки наличия фамилии
            'firstName' => 'Ivan',
            'password' => '123'
        ]));
    }
    // Тест проверяет, что команда действительно требует имя пользователя
    public function testItRequiresFirstName(): void
    {
// Вызываем ту же функцию
        $command = new CreateUserCommand($this->makeUsersRepository(), new DummyLogger());
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('No such argument: firstName');
        $command->handle(new Arguments([
            'login' => 'Ivan',
            'password' => '123'
        ]));
    }

    // Тест, проверяющий, что команда сохраняет пользователя в репозитории
    public function testItSavesUserToRepository(): void
    {
    // Создаём объект анонимного класса
        $usersRepository = new class implements UsersRepositoryInterface {
    // В этом свойстве мы храним информацию о том,
    // был ли вызван метод save
            private bool $called = false;
            public function save(User $user): void
            {
    // Запоминаем, что метод save был вызван
                $this->called = true;
            }
            public function getByUUID(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }
            public function getByLogin(string $login): User
            {
                throw new UserNotFoundException("Not found");
            }
    // Этого метода нет в контракте UsersRepositoryInterface,
    // но ничто не мешает его добавить.
    // С помощью этого метода мы можем узнать,
    // был ли вызван метод save
            public function wasCalled(): bool
            {
                return $this->called;
            }
        };
    // Передаём наш мок в команду
        $command = new CreateUserCommand($usersRepository, new DummyLogger());
    // Запускаем команду
        $command->handle(new Arguments([
            'login' => 'Ivan',
            'firstName' => 'Ivan',
            'lastName' => 'Nikitin',
            'password' => '123'
        ]));
    // Проверяем утверждение относительно мока,
    // а не утверждение относительно команды
        $this->assertTrue($usersRepository->wasCalled());
    }

}