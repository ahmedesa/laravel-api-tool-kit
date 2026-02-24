<?php

declare(strict_types=1);

namespace Essa\APIToolKit\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    /**
     * Return a server error response.
     *
     * @param mixed|null $details Optional error details.
     * @param string|null $message Optional error message.
     *
     * @return JsonResponse Server error JSON response.
     */
    public function responseServerError(mixed $details = null, ?string $message = null): JsonResponse
    {
        return $this->APIError(Response::HTTP_INTERNAL_SERVER_ERROR, $message, $details);
    }

    /**
     * Return a custom error response.
     *
     * @param mixed $title Error title.
     * @param mixed $details Error details.
     * @param int $statusCode HTTP status code.
     *
     * @return JsonResponse Custom error JSON response.
     */
    public function responseWithCustomError(mixed $title, mixed $details, int $statusCode): JsonResponse
    {
        return $this->APIError($statusCode, $title, $details);
    }

    /**
     * Return an unprocessable entity error response.
     *
     * @param mixed|null $details Optional error details.
     * @param string|null $message Optional error message.
     *
     * @return JsonResponse Unprocessable entity JSON response.
     */
    public function responseUnprocessable(mixed $details = null, ?string $message = null): JsonResponse
    {
        return $this->APIError(Response::HTTP_UNPROCESSABLE_ENTITY, $message, $details);
    }

    /**
     * Return a bad request error response.
     *
     * @param mixed|null $details Optional error details.
     * @param string|null $message Optional error message.
     *
     * @return JsonResponse Bad request JSON response.
     */
    public function responseBadRequest(mixed $details = null, ?string $message = null): JsonResponse
    {
        return $this->APIError(Response::HTTP_BAD_REQUEST, $message, $details);
    }

    /**
     * Return a not found error response.
     *
     * @param mixed|null $details Optional error details.
     * @param string|null $message Optional error message.
     *
     * @return JsonResponse Not found JSON response.
     */
    public function responseNotFound(mixed $details = null, ?string $message = 'Record not found!'): JsonResponse
    {
        return $this->APIError(Response::HTTP_NOT_FOUND, $message, $details);
    }

    /**
     * Return an unauthorized error response.
     *
     * @param string $details Optional error details.
     * @param string $message Optional error message.
     *
     * @return JsonResponse Unauthorized JSON response.
     */
    public function responseUnAuthorized(
        string $details = 'you are not authorized to perform this action',
        string $message = 'Unauthorized!'
    ): JsonResponse {
        return $this->APIError(Response::HTTP_FORBIDDEN, $message, $details);
    }

    /**
     * Return an unauthenticated error response.
     *
     * @param string $details Optional error details.
     * @param string $message Optional error message.
     *
     * @return JsonResponse Unauthenticated JSON response.
     */
    public function responseUnAuthenticated(
        string $details = 'you are not authenticated to perform this action',
        string $message = 'Unauthenticated!'
    ): JsonResponse {
        return $this->APIError(Response::HTTP_UNAUTHORIZED, $message, $details);
    }

    /**
     * Return a conflict error response.
     *
     * @param string $details Optional error details.
     * @param string $message Optional error message.
     *
     * @return JsonResponse Conflict error JSON response.
     */
    public function responseConflictError(
        string $details = 'conflict',
        string $message = 'Conflict!'
    ): JsonResponse {
        return $this->APIError(Response::HTTP_CONFLICT, $message, $details);
    }

    /**
     * Return a success response.
     *
     * @param string|null $message Optional success message.
     * @param mixed|null $data Optional data to include in the response.
     *
     * @return JsonResponse Success JSON response.
     */
    public function responseSuccess(?string $message = null, mixed $data = null): JsonResponse
    {
        return new JsonResponse([
            'status' => Response::HTTP_OK,
            'message' => $message,
            'data' => $data,
        ], Response::HTTP_OK);
    }

    /**
     * Return a created response.
     *
     * @param string|null $message Optional created message.
     * @param mixed|null $data Optional data to include in the response.
     *
     * @return JsonResponse Created JSON response.
     */
    public function responseCreated(?string $message = 'Record created successfully', mixed $data = null): JsonResponse
    {
        return new JsonResponse([
            'status' => Response::HTTP_CREATED,
            'message' => $message,
            'data' => $data,
        ], Response::HTTP_CREATED);
    }

    /**
     * Return a deleted response.
     *
     * @return JsonResponse Deleted JSON response.
     */
    public function responseDeleted(): JsonResponse
    {
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Create a JSON response for validation errors.
     *
     * @param ValidationException $exception The validation exception.
     *
     * @return JsonResponse A JSON response containing validation error information.
     */
    public function responseValidationError(ValidationException $exception): JsonResponse
    {
        // Extract validation errors and format them into an array.
        $errors = collect($exception->validator->errors())->map(fn ($error, $key) => [
            'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'title' => 'Validation Error',
            'detail' => $error[0],
            'source' => [
                'pointer' => '/' . str_replace('.', '/', $key),
            ],
        ])->values();

        // Create the JSON response with the formatted errors.
        $responseData = [
            'errors' => $errors,
        ];

        // Set the Content-Type header to specify JSON problem format.
        $headers = [
            'Content-Type' => 'application/problem+json',
        ];

        return new JsonResponse($responseData, Response::HTTP_UNPROCESSABLE_ENTITY, $headers);
    }

    /**
     * Return an accepted response (HTTP 202).
     *
     * Useful for async operations where the request has been accepted for processing
     * but the processing has not been completed.
     *
     * @param string|null $message Optional accepted message.
     * @param mixed|null $data Optional data to include in the response.
     *
     * @return JsonResponse Accepted JSON response.
     */
    public function responseAccepted(?string $message = 'Request accepted for processing', mixed $data = null): JsonResponse
    {
        return new JsonResponse([
            'status' => Response::HTTP_ACCEPTED,
            'message' => $message,
            'data' => $data,
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Return a no content response (HTTP 204).
     *
     * @return JsonResponse No content JSON response.
     */
    public function responseNoContent(): JsonResponse
    {
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Create a JSON response for API errors.
     *
     * @param int $code The HTTP status code for the error.
     * @param string|null $title A brief error description (default: generic message).
     * @param mixed|null $details Additional details about the error (default: null).
     *
     * @return JsonResponse A JSON response containing the error information.
     */
    private function APIError(int $code, ?string $title, mixed $details = null): JsonResponse
    {
        // If no title is provided, use a generic error message.
        $formattedTitle = $title ?? 'Oops. Something went wrong. Please try again or contact support';

        // Create the JSON response with error information.
        $responseData = [
            'errors' => [
                [
                    'status' => $code,
                    'title' => $formattedTitle,
                    'detail' => $details,
                ],
            ],
        ];

        // Set the Content-Type header to specify JSON problem format.
        $headers = [
            'Content-Type' => 'application/problem+json',
        ];

        return new JsonResponse($responseData, $code, $headers);
    }
}
