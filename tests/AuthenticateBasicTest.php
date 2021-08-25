<?php

declare(strict_types=1);

namespace Kafkiansky\SymfonyMiddleware\Tests;

use Kafkiansky\SymfonyMiddleware\AuthenticateBasic;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class AuthenticateBasicTest extends TestCase
{
    public function testAuthenticationPassed(): void
    {
        $middleware = $this->createMiddleware();

        $response = $middleware->process(new ServerRequest('GET', '/'), $this->createRequestHandler());

        self::assertEquals(401, $response->getStatusCode());
        self::assertArrayHasKey('WWW-Authenticate', $response->getHeaders());
        self::assertEquals('Basic realm="test"', $response->getHeader('WWW-Authenticate')[0]);

        $response = $middleware->process(
            new ServerRequest('GET', '/', [], '', '', ['PHP_AUTH_USER' => 'root', 'PHP_AUTH_PW' => 'secret']),
            $this->createRequestHandler()
        );

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals(['success' => true], json_decode((string) $response->getBody(), true));
    }

    public function testPathCanBeExcluded(): void
    {
        $middleware = $this->createMiddleware(excludedPaths: ['/test', '/list']);

        $response = $middleware->process(new ServerRequest('GET', '/test'), $this->createRequestHandler());
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals(['success' => true], json_decode((string) $response->getBody(), true));
    }

    public function testPatternCanBeExluced(): void
    {
        $middleware = $this->createMiddleware(excludedPatterns: ['/users\/\\d+\\/follow/']);

        $response = $middleware->process(new ServerRequest('GET', '/users/20/follow'), $this->createRequestHandler());
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals(['success' => true], json_decode((string) $response->getBody(), true));

        $response = $middleware->process(new ServerRequest('GET', '/posts/20/view'), $this->createRequestHandler());
        self::assertEquals(401, $response->getStatusCode());
    }

    private function createMiddleware(array|null $excludedPaths = null, array|null $excludedPatterns = null): AuthenticateBasic
    {
        return new AuthenticateBasic('root', 'secret', 'test', $excludedPaths, $excludedPatterns);
    }

    private function createRequestHandler(): RequestHandlerInterface
    {
        return new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response(200, [], '{"success": true}');
            }
        };
    }
}
