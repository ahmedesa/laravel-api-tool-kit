<?php

namespace Essa\APIToolKit\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * handle json response.
 */
trait ApiResponse
{
    /**
     * @param null $message
     * @param null $details
     */
    public function responseServerError($details = null, $message = null): JsonResponse
    {
        return $this->APIError(Response::HTTP_INTERNAL_SERVER_ERROR, $message, $details);
    }

    public function responseWithCustomError($title, $details, $status_code): JsonResponse
    {
        return $this->APIError($status_code, $title, $details);
    }

    /**
     * @param null $message
     * @param null $details
     */
    public function responseUnprocessable($details = null, $message = null): JsonResponse
    {
        return $this->APIError(Response::HTTP_UNPROCESSABLE_ENTITY, $message, $details);
    }

    /**
     * @param null $message
     * @param null $details
     */
    public function responseBadRequest($details = null, $message = null): JsonResponse
    {
        return $this->APIError(Response::HTTP_BAD_REQUEST, $message, $details);
    }

    /**
     * @param null $details
     */
    public function responseNotFound($details = null, ?string $message = 'Record not found!'): JsonResponse
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

    /**
     * @param null $message
     * @param null $data
     */
    public function responseSuccess($message = null, $data = null): JsonResponse
    {
        return new JsonResponse([
            'message' => $message,
            'data' => $data,
        ], Response::HTTP_OK);
    }

    /**
     * @param null $data
     */
    public function responseCreated(?string $message = 'Record created successfully', $data = null): JsonResponse
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
                        'pointer' => '/'.str_replace('.', '/', $key),
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

    /**
     * @param null $details
     */
    private function APIError(
        int $code,
        ?string $title,
        $details = null
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
