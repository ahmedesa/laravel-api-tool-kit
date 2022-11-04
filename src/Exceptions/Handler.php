<?php

namespace Essa\APIToolKit\Exceptions;

use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = ['password', 'password_confirmation'];

    public function render($request, $e): JsonResponse
    {
        $this->log($e);

        if ($request->expectsJson()) {
            if ($e instanceof ThrottleRequestsException) {
                return $this->responseWithCustomError(
                    'Too Many Attempts.',
                    'Too Many Attempts Please Try Again Later.',
                    429
                );
            }

            if ($e instanceof ValidationException) {
                return $this->ResponseValidationError($e);
            }

            if ($e instanceof ModelNotFoundException) {
                $id = [] !== $e->getIds() ? ' '.implode(', ', $e->getIds()) : '.';

                $model = class_basename($e->getModel());

                return $this->responseNotFound("{$model} with id {$id} not found", 'Record not found!');
            }

            if ($e instanceof QueryException) {
                if (app()->isProduction()) {
                    return $this->responseServerError();
                }

                return $this->responseNotFound(
                    $e->getMessage(),
                    Str::title(Str::snake(class_basename($e), ' '))
                );
            }

            if ($e instanceof AuthorizationException) {
                return $this->responseUnAuthorized();
            }

            if ($e instanceof NotFoundHttpException) {
                return $this->responseNotFound($e->getMessage());
            }

            if ($e instanceof UnprocessableEntityHttpException) {
                return $this->responseUnprocessable(
                    $e->getMessage(),
                    Str::title(Str::snake(class_basename($e), ' '))
                );
            }

            if ($e instanceof AuthenticationException) {
                return $this->responseUnAuthenticated($e->getMessage());
            }

            if ($e instanceof BadRequestHttpException) {
                return $this->responseBadRequest(
                    $e->getMessage(),
                    Str::title(Str::snake(class_basename($e), ' '))
                );
            }

            if ($e instanceof NotAcceptableHttpException) {
                return $this->responseWithCustomError(
                    'Not Accessible !!',
                    $e->getMessage(),
                    Response::HTTP_NOT_ACCEPTABLE
                );
            }
        }

        return parent::render($request, $e);
    }

    private function log($exception): void
    {
        $logger = $this->container->make(LoggerInterface::class);

        $logger->error($exception->getMessage(), array_merge($this->context(), [
            'exception' => $exception,
        ]));
    }
}
