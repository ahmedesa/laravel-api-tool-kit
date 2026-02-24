<?php

namespace Essa\APIToolKit\Tests;

use Essa\APIToolKit\Api\ApiResponse;
use Essa\APIToolKit\APIToolKit;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Exceptions\Handler;

class APIToolKitTest extends TestCase
{
    use ApiResponse;

    /** @test */
    public function registerExceptionRenderersDoesNotThrow(): void
    {
        $exceptions = $this->createExceptionsConfig();

        APIToolKit::registerExceptionRenderers($exceptions);

        $this->assertTrue(true);
    }

    /** @test */
    public function itRegistersAuthenticationExceptionRenderer(): void
    {
        $response = $this->responseUnAuthenticated('Unauthenticated.');

        $this->assertSame(401, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertSame('Unauthenticated!', $responseData['errors'][0]['title']);
        $this->assertSame('Unauthenticated.', $responseData['errors'][0]['detail']);
    }

    /** @test */
    public function itRegistersAuthorizationExceptionRenderer(): void
    {
        $response = $this->responseUnAuthorized();

        $this->assertSame(403, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertSame('Unauthorized!', $responseData['errors'][0]['title']);
    }

    /** @test */
    public function itRegistersNotFoundExceptionRenderer(): void
    {
        $response = $this->responseNotFound('Route not found.');

        $this->assertSame(404, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertSame('Route not found.', $responseData['errors'][0]['detail']);
    }

    /** @test */
    public function itRegistersThrottleExceptionRenderer(): void
    {
        $response = $this->responseWithCustomError(
            'Too Many Attempts.',
            'Too Many Attempts Please Try Again Later.',
            429
        );

        $this->assertSame(429, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertSame('Too Many Attempts.', $responseData['errors'][0]['title']);
    }

    private function createExceptionsConfig(): Exceptions
    {
        $handler = new Handler($this->app);

        return new Exceptions($handler);
    }
}
