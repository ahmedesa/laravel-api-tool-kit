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
}
