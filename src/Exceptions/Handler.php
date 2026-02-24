<?php

namespace Essa\APIToolKit\Exceptions;

use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request as RequestAlias;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Throwable;

/**
 * Class Handler
 *
 * @deprecated Since v2.1. For Laravel 11+, use {@see \Essa\APIToolKit\APIToolKit::registerExceptionRenderers()} in bootstrap/app.php.
 *             This class will be removed in v3.0.
 *
 * @package Essa\APIToolKit\Exceptions
 */
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

    /**
     * Render an exception into an HTTP response.
     *
     * @param RequestAlias $request
     * @param Throwable $e
     * @return Response
     * @throws Throwable
     */
    public function render($request, $e): Response
    {
        $this->log($e);

        if ($request->expectsJson()) {
            if ($e instanceof ThrottleRequestsException) {
                return $this->responseForThrottleRequestsException();
            }

            if ($e instanceof ValidationException) {
                return $this->responseForValidationException($e);
            }

            if ($e instanceof ModelNotFoundException) {
                return $this->responseForModelNotFoundException($e);
            }

            if ($e instanceof QueryException) {
                return $this->responseForQueryException($e);
            }

            if ($e instanceof AuthorizationException) {
                return $this->responseForAuthorizationException();
            }

            if ($e instanceof NotFoundHttpException) {
                return $this->responseForNotFoundHttpException($e);
            }

            if ($e instanceof UnprocessableEntityHttpException) {
                return $this->responseForUnprocessableEntityHttpException($e);
            }

            if ($e instanceof AuthenticationException) {
                return $this->responseForAuthenticationException($e);
            }

            if ($e instanceof BadRequestHttpException) {
                return $this->responseForBadRequestHttpException($e);
            }

            if ($e instanceof NotAcceptableHttpException) {
                return $this->responseForNotAcceptableHttpException($e);
            }
            if ($e instanceof ConflictHttpException) {
                return $this->responseForConflictHttpException($e);
            }
        }

        return parent::render($request, $e);
    }

    /**
     * Log the given exception.
     *
     * @param Throwable $exception
     * @return void
     * @throws BindingResolutionException
     */
    protected function log(Throwable $exception): void
    {
        $logger = $this->container->make(LoggerInterface::class);

        $logger->error($exception->getMessage(), array_merge($this->context(), [
            'exception' => $exception,
        ]));
    }

    /**
     * Response for NotAcceptableHttpException.
     *
     * @param  NotAcceptableHttpException  $e
     * @return JsonResponse
     */
    protected function responseForNotAcceptableHttpException(NotAcceptableHttpException $e): JsonResponse
    {
        return $this->responseWithCustomError(
            'Not Accessible !!',
            $e->getMessage(),
            Response::HTTP_NOT_ACCEPTABLE
        );
    }

    /**
     * Response for BadRequestHttpException.
     *
     * @param  BadRequestHttpException  $e
     * @return JsonResponse
     */
    protected function responseForBadRequestHttpException(BadRequestHttpException $e): JsonResponse
    {
        return $this->responseBadRequest(
            $e->getMessage(),
            Str::title(Str::snake(class_basename($e), ' '))
        );
    }

    /**
     * Response for AuthenticationException.
     *
     * @param  AuthenticationException  $e
     * @return JsonResponse
     */
    protected function responseForAuthenticationException(AuthenticationException $e): JsonResponse
    {
        return $this->responseUnAuthenticated($e->getMessage());
    }

    /**
     * Response for UnprocessableEntityHttpException.
     *
     * @param  UnprocessableEntityHttpException  $e
     * @return JsonResponse
     */
    protected function responseForUnprocessableEntityHttpException(UnprocessableEntityHttpException $e): JsonResponse
    {
        return $this->responseUnprocessable(
            $e->getMessage(),
            Str::title(Str::snake(class_basename($e), ' '))
        );
    }

    /**
     * Response for NotFoundHttpException.
     *
     * @param  NotFoundHttpException  $e
     * @return JsonResponse
     */
    protected function responseForNotFoundHttpException(NotFoundHttpException $e): JsonResponse
    {
        return $this->responseNotFound($e->getMessage());
    }

    /**
     * Response for AuthorizationException.
     *
     * @return JsonResponse
     */
    protected function responseForAuthorizationException(): JsonResponse
    {
        return $this->responseUnAuthorized();
    }

    /**
     * Response for QueryException.
     *
     * @param  QueryException  $e
     * @return JsonResponse
     */
    protected function responseForQueryException(QueryException $e): JsonResponse
    {
        if (app()->isProduction()) {
            return $this->responseServerError();
        }

        return $this->responseNotFound(
            $e->getMessage(),
            Str::title(Str::snake(class_basename($e), ' '))
        );
    }

    /**
     * Response for ModelNotFoundException.
     *
     * @param  ModelNotFoundException  $e
     * @return JsonResponse
     */
    protected function responseForModelNotFoundException(ModelNotFoundException $e): JsonResponse
    {
        $id = [] !== $e->getIds() ? ' ' . implode(', ', $e->getIds()) : '.';

        $model = class_basename($e->getModel());

        return $this->responseNotFound("{$model} with id {$id} not found", 'Record not found!');
    }

    /**
     * Response for ValidationException.
     *
     * @param  ValidationException  $e
     * @return JsonResponse
     */
    protected function responseForValidationException(ValidationException $e): JsonResponse
    {
        return $this->ResponseValidationError($e);
    }

    /**
     * Response for ThrottleRequestsException.
     *
     * @return JsonResponse
     */
    protected function responseForThrottleRequestsException(): JsonResponse
    {
        return $this->responseWithCustomError(
            'Too Many Attempts.',
            'Too Many Attempts Please Try Again Later.',
            429
        );
    }

    /**
     * Response for ConflictHttpException.
     *
     * @return JsonResponse
     */
    protected function responseForConflictHttpException(ConflictHttpException $e): JsonResponse
    {
        return $this->responseConflictError($e->getMessage());
    }
}
