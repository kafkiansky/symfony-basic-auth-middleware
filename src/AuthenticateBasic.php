<?php

declare(strict_types=1);

namespace Kafkiansky\SymfonyMiddleware;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class AuthenticateBasic implements MiddlewareInterface
{
    public function __construct(
        private string $user,
        private string $password,
        private string $realm,
        private array|null $excludedPaths = null,
        private array|null $excludedPatterns = null,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();

        if ($this->excludedPaths !== null && in_array($path, $this->excludedPaths)) {
            return $handler->handle($request);
        }

        if ($this->excludedPatterns !== null && $this->isMatchedByPattern($path)) {
            return $handler->handle($request);
        }

        if ($this->isCredentialsMatched($request)) {
            return $handler->handle($request);
        }

        return new Response(401, [
            'WWW-Authenticate' => 'Basic realm="'.$this->realm.'"'
        ]);
    }

    private function isCredentialsMatched(ServerRequestInterface $request): bool
    {
        $user = $request->getServerParams()['PHP_AUTH_USER'] ?? null;
        $passwd = $request->getServerParams()['PHP_AUTH_PW'] ?? null;

        return $user === $this->user && $passwd === $this->password;
    }

    private function isMatchedByPattern(string $path): bool
    {
        foreach ($this->excludedPatterns as $excludedPattern) {
            if (preg_match($excludedPattern, $path)) {
                return true;
            }
        }

        return false;
    }
}
