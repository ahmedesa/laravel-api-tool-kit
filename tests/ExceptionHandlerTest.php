<?php

namespace Essa\APIToolKit\Tests;

use Essa\APIToolKit\Exceptions\Handler;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class ExceptionHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function HandlesAuthenticationException(): void
    {
        $exception = new AuthenticationException('Unauthenticated.');
        $this->assertExceptionHandling($exception, 401, 'Unauthenticated!', 'Unauthenticated.');
    }

    /**
     * @test
     */
    public function HandlesNotFoundHttpException(): void
    {
        $exception = new NotFoundHttpException('Route not found.');
        $this->assertExceptionHandling($exception, 404, 'Record not found!', 'Route not found.');
    }

    /**
     * @test
     */
    public function HandlesUnprocessableEntityHttpException(): void
    {
        $exception = new UnprocessableEntityHttpException('Validation failed.');
        $this->assertExceptionHandling($exception, 422, 'Unprocessable Entity Http Exception', 'Validation failed.');
    }

    /**
     * @test
     */
    public function HandlesModelNotFoundException(): void
    {
        $exception = new ModelNotFoundException();

        $exception->setModel('User', [1, 2, 3]);
        $this->assertExceptionHandling($exception, 404, 'Record not found!', 'User with id  1, 2, 3 not found');
    }

    /**
     * @test
     */
    public function HandlesAuthorizationException(): void
    {
        $exception = new AuthorizationException('Unauthorized action.');
        $this->assertExceptionHandling($exception, 403, 'Unauthorized!', 'you are not authorized to perform this action');
    }

    /**
     * @test
     */
    public function HandlesValidationException(): void
    {
        $exception = new ValidationException(
            validator([], ['name' => 'required']),
            $this->createMock(Request::class)
        );
        $this->assertExceptionHandling($exception, 422, 'Validation Error', 'The name field is required.');
    }

    /**
     * @test
     */
    public function HandlesBadRequestHttpException(): void
    {
        $exception = new BadRequestHttpException('Bad request.');
        $this->assertExceptionHandling($exception, 400, 'Bad Request Http Exception', 'Bad request.');
    }

    /**
     * @test
     */
    public function HandlesNotAcceptableHttpException(): void
    {
        $exception = new NotAcceptableHttpException('Not acceptable.');
        $this->assertExceptionHandling($exception, 406, 'Not Accessible !!', 'Not acceptable.');
    }

    /**
     * @test
     */
    public function HandlesThrottleRequestsException(): void
    {
        $exception = new ThrottleRequestsException('Too many attempts.');
        $this->assertExceptionHandling($exception, 429, 'Too Many Attempts.', 'Too Many Attempts Please Try Again Later.');
    }

    private function assertExceptionHandling(Exception $exception, int $statusCode, string $title, string $detail): void
    {
        $handler = new Handler($this->app);
        $request = Request::create('/', 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
        $response = $handler->render($request, $exception);

        $this->assertSame($statusCode, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('errors', $responseData);
        $this->assertCount(1, $responseData['errors']);
        $error = $responseData['errors'][0];

        $this->assertSame($statusCode, $error['status']);
        $this->assertSame($title, $error['title']);
        $this->assertSame($detail, $error['detail']);
    }
}
