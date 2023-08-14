<?php

namespace Essa\APIToolKit\Api;

use GuzzleHttp\Client;

trait ConsumesExternalServices
{
    public function makeRequest(
        string $method,
        string $requestUrl,
        array $queryParams = [],
        array $formParams = [],
        array $headers = [],
        bool $isJsonRequest = false,
        bool $decodeResponse = true
    ) {
        $client = new Client([
            'base_uri' => $this->baseUri,
        ]);

        if (method_exists($this, 'resolveAuthorization') && ! $headers) {
            $this->resolveAuthorization($queryParams, $formParams, $headers);
        }

        $response = $client->request($method, $requestUrl, [
            $isJsonRequest ? 'json' : 'form_params' => $formParams,
            'headers' => $headers,
            'query' => $queryParams,
        ]);

        $responseContent = $response->getBody()->getContents();

        return $decodeResponse ? json_decode($responseContent) : $responseContent;
    }
}
