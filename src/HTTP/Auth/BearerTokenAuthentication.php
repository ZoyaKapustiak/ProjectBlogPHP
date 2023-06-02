<?php

namespace ZoiaProjects\ProjectBlog\HTTP\Auth;

use DateTimeImmutable;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\AuthException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\AuthTokenNotFoundException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\HttpException;
use ZoiaProjects\ProjectBlog\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\Repositories\UserRepository\UsersRepositoryInterface;
use ZoiaProjects\ProjectBlog\Blog\User;
use ZoiaProjects\ProjectBlog\HTTP\Request;

class BearerTokenAuthentication implements TokenAuthenticationInterface
{
    private const HEADER_PREFIX = 'Bearer ';

    public function __construct(
        private readonly AuthTokensRepositoryInterface $authTokensRepository,
        private readonly UsersRepositoryInterface      $usersRepository,
    ){}

    /**
     * @throws AuthException
     */
    public function user(Request $request): User
    {
        $token = $this->getAuthTokenString($request);
// Ищем токен в репозитории
        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException) {
            throw new AuthException("Bad token: [$token]");
        }
// Проверяем срок годности токена
        if ($authToken->getExpiresOn() <= new DateTimeImmutable()) {
            throw new AuthException("Token expired: [$token]");
        }
// Получаем UUID пользователя из токена
        $userUuid = $authToken->getUserUuid();
// Ищем и возвращаем пользователя
        return $this->usersRepository->getByUUID($userUuid);
    }

    /**
     * @throws AuthException
     */
    public function getAuthTokenString(Request $request): string
    {
        // Получаем HTTP-заголовок
        try {
            $header = $request->header('Authorization');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }
        // Проверяем, что заголовок имеет правильный формат
        if (!str_starts_with($header, self::HEADER_PREFIX)) {
            throw new AuthException("Malformed token: [$header]");
        }
        // Отрезаем префикс Bearer
        return mb_substr($header, strlen(self::HEADER_PREFIX));

    }

}