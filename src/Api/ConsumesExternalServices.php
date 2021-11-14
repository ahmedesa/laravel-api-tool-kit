<?php

namespace Essa\APIToolKit\Api;

use GuzzleHttp\Client;

trait ConsumesExternalServices
{
    public function makeRequest(
        $method,
        $requestUrl,
        $queryParams = [],
        $formParams = [],
        $headers = [],
        $isJsonRequest = false
    )
    {
        $client = new Client([
            'base_uri' => $this->baseUri,
        ]);

        if (method_exists($this, 'resolveAuthorization') && !$headers) {
            $this->resolveAuthorization($queryParams, $formParams, $headers);
        }

        $response = $client->request($method, $requestUrl, [
            $isJsonRequest ? 'json' : 'form_params' => $formParams,
            'headers' => $headers,
            'query' => $queryParams,
        ]);

        return json_decode($response->getBody()->getContents());
    }
}
