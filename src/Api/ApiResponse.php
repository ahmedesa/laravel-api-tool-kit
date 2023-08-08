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
     * @param  null  $message
     * @param  null  $details
     */
    public function responseServerError($details = null, $message = null): JsonResponse
    {
        // Implement the code correctly for responseServerError method
        return $this->APIError(Response::HTTP_INTERNAL_SERVER_ERROR, $message, $details);
    }

    public function responseWithCustomError($title, $details, $status_code): JsonResponse
    {
        // Implement the code correctly for responseWithCustomError method
        return $this->APIError($status_code, $title, $details);
    }

    /**
     * @param  null  $message
     * @param  null  $details
     */
    public function responseUnprocessable($details = null, $message = null): JsonResponse
    {
        // Implement the code correctly for responseUnprocessable method
        return $this->APIError(Response::HTTP_UNPROCESSABLE_ENTITY, $message, $details);
    }

    /**
     * @param  null  $message
     * @param  null  $details
     */
    public function responseBadRequest($details = null, $message = null): JsonResponse
    {
        // Implement the code correctly for responseBadRequest method
        return $this->APIError(Response::HTTP_BAD_REQUEST, $message, $details);
    }

    /**
     * @param  null  $details
     */
    public function responseNotFound($details = null, ?string $message = 'Record not found!'): JsonResponse
    {
        // Implement the code correctly for responseNotFound method
        return $this->APIError(Response::HTTP_NOT_FOUND, $message, $details);
    }

    public function responseUnAuthorized(
        string $details = 'you are not authorized to preform this actions',
        string $message = 'Unauthorized!'
    ): JsonResponse {
        // Implement the code correctly for responseUnAuthorized method
        return $this->APIError(Response::HTTP_FORBIDDEN, $message, $details);
    }

    public function responseUnAuthenticated(
        string $details = 'you are not authenticated to preform this actions',
        string $message = 'unauthenticated!'
    ): JsonResponse {
        // Implement the code correctly for responseUnAuthenticated method
        return $this->APIError(Response::HTTP_UNAUTHORIZED, $message, $details);
    }

    public function responseConflictError(
        string $details = 'conflict',
        string $message = 'conflict!'
    ): JsonResponse {
        // Implement the code correctly for responseConflictError method
        return $this->APIError(Response::HTTP_CONFLICT, $message, $details);
    }

    /**
     * @param  null  $message
     * @param  null  $data
     */
    public function responseSuccess($message = null, $data = null): JsonResponse
    {
        // Implement the code correctly for responseSuccess method
        return new JsonResponse([
            'message' => $message,
            'data' => $data,
        ], Response::HTTP_OK);
    }

    /**
     * @param  null  $data
     */
    public function responseCreated(?string $message = 'Record created successfully', $data = null): JsonResponse
    {
        // Implement the code correctly for responseCreated method
        return new JsonResponse([
            'message' => $message,
            'data' => $data,
        ], Response::HTTP_CREATED);
    }

    public function responseDeleted(): JsonResponse
    {
        // Implement the code correctly for responseDeleted method
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    public function ResponseValidationError(ValidationException $exception): JsonResponse
    {
        // Implement the code correctly for ResponseValidationError method
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

    /**
     * @param  null  $details
     */
    private function APIError(
        int $code,
        ?string $title,
        $details = null
    ): JsonResponse {
        // Testable code for APIError method
    }
}