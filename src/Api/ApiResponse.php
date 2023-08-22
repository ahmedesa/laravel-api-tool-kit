<?php

namespace Essa\APIToolKit\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    public function responseServerError(mixed $details = null, ?string $message = null): JsonResponse
    {
        return $this->APIError(Response::HTTP_INTERNAL_SERVER_ERROR, $message, $details);
    }

    public function responseWithCustomError(mixed $title, $details, int $statusCode): JsonResponse
    {
        return $this->APIError($statusCode, $title, $details);
    }

    public function responseUnprocessable(mixed $details = null, ?string $message = null): JsonResponse
    {
        return $this->APIError(Response::HTTP_UNPROCESSABLE_ENTITY, $message, $details);
    }

    public function responseBadRequest(mixed $details = null, ?string $message = null): JsonResponse
    {
        return $this->APIError(Response::HTTP_BAD_REQUEST, $message, $details);
    }

    public function responseNotFound(mixed $details = null, ?string $message = 'Record not found!'): JsonResponse
    {
        return $this->APIError(Response::HTTP_NOT_FOUND, $message, $details);
    }

    public function responseUnAuthorized(
        string $details = 'you are not authorized to preform this actions',
        string $message = 'Unauthorized!'
    ): JsonResponse {
        return $this->APIError(Response::HTTP_FORBIDDEN, $message, $details);
    }

    public function responseUnAuthenticated(
        string $details = 'you are not authenticated to preform this actions',
        string $message = 'unauthenticated!'
    ): JsonResponse {
        return $this->APIError(Response::HTTP_UNAUTHORIZED, $message, $details);
    }

    public function responseConflictError(
        string $details = 'conflict',
        string $message = 'conflict!'
    ): JsonResponse {
        return $this->APIError(Response::HTTP_CONFLICT, $message, $details);
    }

    public function responseSuccess(?string $message = null, mixed $data = null): JsonResponse
    {
        return new JsonResponse([
            'message' => $message,
            'data' => $data,
        ], Response::HTTP_OK);
    }

    /**
     * @param  null  $data
     */
    public function responseCreated(?string $message = 'Record created successfully', mixed $data = null): JsonResponse
    {
        return new JsonResponse([
            'message' => $message,
            'data' => $data,
        ], Response::HTTP_CREATED);
    }

    public function responseDeleted(): JsonResponse
    {
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    public function ResponseValidationError(ValidationException $exception): JsonResponse
    {
        $errors = (new Collection($exception->validator->errors()))
            ->map(function ($error, $key): array {
                return [
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'title' => 'Validation Error',
                    'detail' => $error[0],
                    'source' => [
                        'pointer' => '/' . str_replace('.', '/', $key),
                    ],
                ];
            })
            ->values();

        return new JsonResponse(
            [
                'errors' => $errors,
            ],
            Response::HTTP_UNPROCESSABLE_ENTITY,
            [
                'Content-Type' => 'application/problem+json',
            ]
        );
    }

    private function APIError(
        int $code,
        ?string $title,
        mixed $details = null
    ): JsonResponse {
        return new JsonResponse(
            [
                'errors' => [
                    [
                        'status' => $code,
                        'title' => $title ?? 'Oops . Something went wrong , try again or contact the support',
                        'detail' => $details,
                    ],
                ],
            ],
            $code,
            [
                'Content-Type' => 'application/problem+json',
            ]
        );
    }
}
