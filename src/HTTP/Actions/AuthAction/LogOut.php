<?php

namespace ZoiaProjects\ProjectBlog\HTTP\Actions\AuthAction;

use ZoiaProjects\ProjectBlog\Blog\AuthToken;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\AuthException;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\AuthTokenNotFoundException;
use ZoiaProjects\ProjectBlog\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use ZoiaProjects\ProjectBlog\HTTP\Auth\BearerTokenAuthentication;
use ZoiaProjects\ProjectBlog\HTTP\Auth\PasswordAuthenticationInterface;
use ZoiaProjects\ProjectBlog\HTTP\Request;
use ZoiaProjects\ProjectBlog\HTTP\Response;
use ZoiaProjects\ProjectBlog\HTTP\ErrorResponse;
use ZoiaProjects\ProjectBlog\HTTP\SuccessfulResponse;

class LogOut
{
    public function __construct(
        private readonly AuthTokensRepositoryInterface $authTokensRepository,
        private readonly BearerTokenAuthentication $authentication
    )
    {
    }

    /**
     * @throws AuthException
     */
    public function handle(Request $request): Response
    {
        $token = $this->authentication->getAuthTokenString($request);
        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }

        $authToken->setExpiresOn(new \DateTimeImmutable("now"));

        $this->authTokensRepository->save($authToken);

        return new SuccessfulResponse([
            'token' => $authToken->getToken()
        ]);
    }
}