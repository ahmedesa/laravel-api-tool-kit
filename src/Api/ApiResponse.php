<?php

namespace Essa\APIToolKit\Api;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

/**
 *  handle json response.
 */
trait ApiResponse
{
    /**
     * @param int $code
     * @param string $title
     * @param null $details
     * @return JsonResponse
     */
    private function APIError(
        int $code,
        $title = 'Oops . Something went wrong , try again or contact the support',
        $details = null
    ): JsonResponse
    {
        return response()->json(
            [
                'errors' => [
                    [
                        'status' => $code,
                        'title' => $title,
                        'detail' => $details,
                    ],
                ],
            ],
            $code,
            ['Content-Type' => 'application/problem+json']
        );
    }

    /**
     * @param null $message
     * @param null $details
     * @return JsonResponse
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
     * @return JsonResponse
     */
    public function responseUnprocessable($details = null, $message = null): JsonResponse
    {
        return $this->APIError(Response::HTTP_UNPROCESSABLE_ENTITY, $message, $details);
    }

    /**
     * @param null $message
     * @param null $details
     * @return JsonResponse
     */
    public function responseBadRequest($details = null, $message = null): JsonResponse
    {
        return $this->APIError(Response::HTTP_BAD_REQUEST, $message, $details);
    }

    /**
     * @param string $message
     * @param null $details
     * @return JsonResponse
     */
    public function responseNotFound($details = null, $message = 'Record not found!'): JsonResponse
    {
        return $this->APIError(Response::HTTP_NOT_FOUND, $message, $details);
    }

    /**
     * @param string $message
     * @param string $details
     * @return JsonResponse
     */
    public function responseUnAuthorized(
        $details = 'you are not authorized to preform this actions',
        $message = 'Unauthorized!'
    ): JsonResponse
    {
        return $this->APIError(Response::HTTP_FORBIDDEN, $message, $details);
    }

    /**
     * @param string $message
     * @param string $details
     * @return JsonResponse
     */
    public function responseUnAuthenticated(
        $details = 'you are not authenticated to preform this actions',
        $message = 'unauthenticated!'
    ): JsonResponse
    {
        return $this->APIError(Response::HTTP_UNAUTHORIZED, $message, $details);
    }

    /**
     * @param string $message
     * @param string $details
     * @return JsonResponse
     */
    public function responseConflictError(
        $details = 'conflict',
        $message = 'conflict!'
    ): JsonResponse
    {
        return $this->APIError(Response::HTTP_CONFLICT, $message, $details);
    }

    /**
     * @param null $message
     * @param null $data
     * @return JsonResponse
     */
    public function responseSuccess(
        $message = null,
        $data = null
    ): JsonResponse
    {
        return response()->json(
            [
                'message' => $message,
                'data' => $data,
            ],
            Response::HTTP_OK
        );
    }

    /**
     * @param string $message
     * @param null $data
     * @return JsonResponse
     */
    public function responseCreated(
        $message = 'Record created successfully',
        $data = null
    ): JsonResponse
    {
        return response()->json(
            [
                'message' => $message,
                'data' => $data,
            ],
            Response::HTTP_CREATED
        );
    }

    public function responseDeleted(): JsonResponse
    {
        return response()->json(
            null,
            Response::HTTP_NO_CONTENT
        );
    }

    public function ResponseValidationError($exception): JsonResponse
    {
        $errors = (new Collection($exception->validator->errors()))
            ->map(function ($error, $key) {
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

        return response()->json(
            [
                'errors' => $errors,
            ],
            Response::HTTP_UNPROCESSABLE_ENTITY,
            ['Content-Type' => 'application/problem+json']
        );
    }
}
