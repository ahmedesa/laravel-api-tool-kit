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
        // Testable code for responseServerError method
    }

    public function responseWithCustomError($title, $details, $status_code): JsonResponse
    {
        // Testable code for responseWithCustomError method
    }

    /**
     * @param  null  $message
     * @param  null  $details
     */
    public function responseUnprocessable($details = null, $message = null): JsonResponse
    {
        // Testable code for responseUnprocessable method
    }

    /**
     * @param  null  $message
     * @param  null  $details
     */
    public function responseBadRequest($details = null, $message = null): JsonResponse
    {
        // Testable code for responseBadRequest method
    }

    /**
     * @param  null  $details
     */
    public function responseNotFound($details = null, ?string $message = 'Record not found!'): JsonResponse
    {
        // Testable code for responseNotFound method
    }

    public function responseUnAuthorized(
        string $details = 'you are not authorized to preform this actions',
        string $message = 'Unauthorized!'
    ): JsonResponse {
        // Testable code for responseUnAuthorized method
    }

    public function responseUnAuthenticated(
        string $details = 'you are not authenticated to preform this actions',
        string $message = 'unauthenticated!'
    ): JsonResponse {
        // Testable code for responseUnAuthenticated method
    }

    public function responseConflictError(
        string $details = 'conflict',
        string $message = 'conflict!'
    ): JsonResponse {
        // Testable code for responseConflictError method
    }

    /**
     * @param  null  $message
     * @param  null  $data
     */
    public function responseSuccess($message = null, $data = null): JsonResponse
    {
        // Testable code for responseSuccess method
    }

    /**
     * @param  null  $data
     */
    public function responseCreated(?string $message = 'Record created successfully', $data = null): JsonResponse
    {
        // Testable code for responseCreated method
    }

    public function responseDeleted(): JsonResponse
    {
        // Testable code for responseDeleted method
    }

    public function ResponseValidationError(ValidationException $exception): JsonResponse
    {
        // Testable code for ResponseValidationError method
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