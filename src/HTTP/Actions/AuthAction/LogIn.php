<?php

namespace ZoiaProjects\ProjectBlog\HTTP\Actions\AuthAction;

use TheSeer\Tokenizer\Token;
use ZoiaProjects\ProjectBlog\Blog\AuthToken;
use ZoiaProjects\ProjectBlog\Blog\Exceptions\AuthException;
use ZoiaProjects\ProjectBlog\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use ZoiaProjects\ProjectBlog\HTTP\Actions\ActionInterface;
use ZoiaProjects\ProjectBlog\HTTP\Auth\PasswordAuthenticationInterface;
use ZoiaProjects\ProjectBlog\HTTP\ErrorResponse;
use ZoiaProjects\ProjectBlog\HTTP\Request;
use ZoiaProjects\ProjectBlog\HTTP\Response;
use ZoiaProjects\ProjectBlog\HTTP\SuccessfulResponse;

class LogIn implements ActionInterface
{
    public function __construct(
        private PasswordAuthenticationInterface $passwordAuthentication,
        private AuthTokensRepositoryInterface $authTokensRepository
    )
    {
    }

    public function handle(Request $request): Response
    {
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $authToken = new AuthToken(
            bin2hex(random_bytes(40)),
            $user->uuid(),
            (new \DateTimeImmutable())->modify('+ 1 day')
        );
        $this->authTokensRepository->save($authToken);
        return new SuccessfulResponse([
            'token' => $authToken->getToken(),
        ]);
    }
}