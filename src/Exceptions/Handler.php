<?php

namespace Essa\APIToolKit\Exceptions;

use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;
use Illuminate\Http\Response;
use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function render($request, $exception)
    {
        $this->log($exception);

        if ($request->expectsJson()) {
            if ($exception instanceof ThrottleRequestsException) {
                return $this->responseWithCustomError(
                    'Too Many Attempts.',
                    'Too Many Attempts Please Try Again Later.',
                    429
                );
            }

            if ($exception instanceof ValidationException) {
                return $this->ResponseValidationError($exception);
            }

            if ($exception instanceof ModelNotFoundException) {
                $id = count($exception->getIds()) > 0 ? ' ' . implode(', ', $exception->getIds()) : '.';

                $model = class_basename($exception->getModel());

                return $this->responseNotFound(
                    "{$model} with id {$id} not found",
                    'Record not found!'
                );
            }

            if ($exception instanceof QueryException) {
                if (app()->isProduction()) {
                    return $this->responseServerError();
                }

                return $this->responseNotFound(
                    $exception->getMessage(),
                    Str::title(Str::snake(class_basename($exception), ' '))
                );
            }

            if ($exception instanceof AuthorizationException) {
                return $this->responseUnAuthorized();
            }

            if ($exception instanceof UnprocessableEntityHttpException) {
                return $this->responseUnprocessable(
                    $exception->getMessage(),
                    Str::title(Str::snake(class_basename($exception), ' '))
                );
            }

            if ($exception instanceof AuthenticationException) {
                return $this->responseUnAuthenticated($exception->getMessage());
            }

            if ($exception instanceof BadRequestHttpException) {
                return $this->responseBadRequest(
                    $exception->getMessage(),
                    Str::title(Str::snake(class_basename($exception), ' '))
                );
            }

            if ($exception instanceof NotAcceptableHttpException) {
                return $this->responseWithCustomError(
                    'Not Accessible !!',
                    $exception->getMessage(),
                    Response::HTTP_NOT_ACCEPTABLE
                );
            }
        }

        return parent::render($request, $exception);
    }

    private function log($exception)
    {
        $logger = $this->container->make(LoggerInterface::class);

        $logger->error(
            $exception->getMessage(),
            array_merge($this->context(), ['exception' => $exception])
        );
    }
}
