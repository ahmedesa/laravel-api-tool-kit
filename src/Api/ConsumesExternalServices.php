<?php

declare(strict_types=1);

namespace Essa\APIToolKit\Api;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

trait ConsumesExternalServices
{
    /**
     * Send a request to an external service.
     *
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $requestUrl The URL for the request
     * @param array $data Data to send in the request
     * @param array $headers Headers to include
     * @param bool $isJsonRequest Whether to send as JSON
     *
     * @return mixed The response data (decoded JSON or raw body)
     */
    public function makeRequest(
        string $method,
        string $requestUrl,
        array $data = [],
        array $headers = [],
        bool $isJsonRequest = true
    ): mixed {
        $request = Http::baseUrl($this->baseUri)
            ->withHeaders($headers)
            ->timeout($this->timeout ?? 30)
            ->connectTimeout($this->connectTimeout ?? 10);

        if (isset($this->retries)) {
            $request->retry($this->retries, $this->retryDelay ?? 100);
        }

        if (method_exists($this, 'resolveAuthorization')) {
            $request = $this->resolveAuthorization($request);
        }

        /** @var Response $response */
        $response = $request->{mb_strtolower($method)}($requestUrl, $data);

        if ($response->failed()) {
            return $this->handleRequestError($response);
        }

        return $response->json();
    }

    /**
     * Handle failed request.
     * Override this method to customize error handling.
     */
    protected function handleRequestError(Response $response): mixed
    {
        return $response->throw();
    }
}
