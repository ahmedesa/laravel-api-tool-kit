<?php

namespace Essa\APIToolKit;

use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Main entry point for API Toolkit functionality.
 *
 * Provides a modern way to register exception renderers for Laravel 11+.
 *
 * Usage in bootstrap/app.php:
 *
 *   ->withExceptions(function (Exceptions $exceptions) {
 *       \Essa\APIToolKit\APIToolKit::registerExceptionRenderers($exceptions);
 *   })
 */
class APIToolKit
{
    use ApiResponse;

    /**
     * Register all API exception renderers on the given Exceptions handler.
     *
     * This is the modern replacement for the deprecated Handler class.
     * Use this in your bootstrap/app.php for Laravel 11+.
     *
     * @param Exceptions $exceptions
     * @return void
     */
    public static function registerExceptionRenderers(Exceptions $exceptions): void
    {
        $instance = new static();

        $exceptions->renderable(function (ThrottleRequestsException $e, $request) use ($instance) {
            if ($request->expectsJson()) {
                return $instance->responseWithCustomError(
                    'Too Many Attempts.',
                    'Too Many Attempts Please Try Again Later.',
                    429
                );
            }
        });

        $exceptions->renderable(function (ValidationException $e, $request) use ($instance) {
            if ($request->expectsJson()) {
                return $instance->ResponseValidationError($e);
            }
        });

        $exceptions->renderable(function (ModelNotFoundException $e, $request) use ($instance) {
            if ($request->expectsJson()) {
                $id = [] !== $e->getIds() ? ' ' . implode(', ', $e->getIds()) : '.';
                $model = class_basename($e->getModel());

                return $instance->responseNotFound("{$model} with id {$id} not found", 'Record not found!');
            }
        });

        $exceptions->renderable(function (QueryException $e, $request) use ($instance) {
            if ($request->expectsJson()) {
                if (app()->isProduction()) {
                    return $instance->responseServerError();
                }

                return $instance->responseNotFound(
                    $e->getMessage(),
                    Str::title(Str::snake(class_basename($e), ' '))
                );
            }
        });

        $exceptions->renderable(function (AuthorizationException $e, $request) use ($instance) {
            if ($request->expectsJson()) {
                return $instance->responseUnAuthorized();
            }
        });

        $exceptions->renderable(function (NotFoundHttpException $e, $request) use ($instance) {
            if ($request->expectsJson()) {
                return $instance->responseNotFound($e->getMessage());
            }
        });

        $exceptions->renderable(function (UnprocessableEntityHttpException $e, $request) use ($instance) {
            if ($request->expectsJson()) {
                return $instance->responseUnprocessable(
                    $e->getMessage(),
                    Str::title(Str::snake(class_basename($e), ' '))
                );
            }
        });

        $exceptions->renderable(function (AuthenticationException $e, $request) use ($instance) {
            if ($request->expectsJson()) {
                return $instance->responseUnAuthenticated($e->getMessage());
            }
        });

        $exceptions->renderable(function (BadRequestHttpException $e, $request) use ($instance) {
            if ($request->expectsJson()) {
                return $instance->responseBadRequest(
                    $e->getMessage(),
                    Str::title(Str::snake(class_basename($e), ' '))
                );
            }
        });

        $exceptions->renderable(function (NotAcceptableHttpException $e, $request) use ($instance) {
            if ($request->expectsJson()) {
                return $instance->responseWithCustomError(
                    'Not Accessible !!',
                    $e->getMessage(),
                    Response::HTTP_NOT_ACCEPTABLE
                );
            }
        });

        $exceptions->renderable(function (ConflictHttpException $e, $request) use ($instance) {
            if ($request->expectsJson()) {
                return $instance->responseConflictError($e->getMessage());
            }
        });
    }
}
