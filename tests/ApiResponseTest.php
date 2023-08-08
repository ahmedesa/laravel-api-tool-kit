<?php

namespace Essa\APIToolKit\Tests;

use Essa\APIToolKit\Api\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseTest extends TestCase
{
    private $classThatImplementTheTrait;

    public function setUp(): void
    {
        $this->classThatImplementTheTrait = new class
        {
            use ApiResponse;
        };

        parent::setUp();
    }

    /**
     * @test
     */
    public function responseServerError()
    {
        $response = $this->classThatImplementTheTrait->responseServerError('Server error details', 'Server error message');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function responseSuccess()
    {
        $response = $this->classThatImplementTheTrait->responseSuccess('Success message', ['data' => 'value']);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseData = $response->getData();
        $this->assertEquals('Success message', $responseData->message);
        $this->assertEquals(['data' => 'value'], (array) $responseData->data);
    }

    /**
     * @test
     */
    public function responseValidationError()
    {
        $validator = Validator::make([], [
            'field' => 'required',
        ]);

        try {
            $validator->validate();
        } catch (ValidationException $e) {
            $response = $this->classThatImplementTheTrait->ResponseValidationError($e);

            $this->assertInstanceOf(JsonResponse::class, $response);
            $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());

            $responseData = $response->getData();

            $this->assertIsArray($responseData->errors);
            $this->assertCount(1, $responseData->errors);

            $error = $responseData->errors[0];
            $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $error->status);
            $this->assertEquals('Validation Error', $error->title);
            $this->assertEquals('The field field is required.', $error->detail);
            $this->assertEquals(['pointer' => '/field'], (array) $error->source);
        }
    }

    /**
     * @test
     */
    public function responseWithCustomError()
    {
        $response = $this->classThatImplementTheTrait->responseWithCustomError('Custom error title', 'Custom error details', Response::HTTP_BAD_REQUEST);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $responseData = $response->getData();
        $this->assertEquals('Custom error title', $responseData->title);
        $this->assertEquals('Custom error details', $responseData->details);
    }

    /**
     * @test
     */
    public function responseUnprocessable()
    {
        $response = $this->classThatImplementTheTrait->responseUnprocessable('Unprocessable details', 'Unprocessable message');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $responseData = $response->getData();
        $this->assertEquals('Unprocessable details', $responseData->details);
        $this->assertEquals('Unprocessable message', $responseData->message);
    }

    /**
     * @test
     */
    public function responseBadRequest()
    {
        $response = $this->classThatImplementTheTrait->responseBadRequest('Bad request details', 'Bad request message');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $responseData = $response->getData();
        $this->assertEquals('Bad request details', $responseData->details);
        $this->assertEquals('Bad request message', $responseData->message);
    }

    /**
     * @test
     */
    public function responseNotFound()
    {
        $response = $this->classThatImplementTheTrait->responseNotFound('Not found details', 'Not found message');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $responseData = $response->getData();
        $this->assertEquals('Not found details', $responseData->details);
        $this->assertEquals('Not found message', $responseData->message);
    }

    /**
     * @test
     */
    public function responseUnAuthorized()
    {
        $response = $this->classThatImplementTheTrait->responseUnAuthorized('Unauthorized details', 'Unauthorized message');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        $responseData = $response->getData();
        $this->assertEquals('Unauthorized details', $responseData->details);
        $this->assertEquals('Unauthorized message', $responseData->message);
    }

    /**
     * @test
     */
    public function responseUnAuthenticated()
    {
        $response = $this->classThatImplementTheTrait->responseUnAuthenticated('Unauthenticated details', 'Unauthenticated message');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $responseData = $response->getData();
        $this->assertEquals('Unauthenticated details', $responseData->details);
        $this->assertEquals('Unauthenticated message', $responseData->message);
    }

    /**
     * @test
     */
    public function responseConflictError()
    {
        $response = $this->classThatImplementTheTrait->responseConflictError('Conflict details', 'Conflict message');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $responseData = $response->getData();
        $this->assertEquals('Conflict details', $responseData->details);
        $this->assertEquals('Conflict message', $responseData->message);
    }

    /**
     * @test
     */
    public function responseCreated()
    {
        $response = $this->classThatImplementTheTrait->responseCreated('Record created successfully', ['data' => 'value']);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $responseData = $response->getData();
        $this->assertEquals('Record created successfully', $responseData->message);
        $this->assertEquals(['data' => 'value'], (array) $responseData->data);
    }
}