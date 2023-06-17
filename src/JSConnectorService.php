<?php

namespace Amohamed\JSConnector;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class JSConnectorService
{
    protected $baseUrl;

    protected $client;

    public function __construct()
    {
        $handlerStack = HandlerStack::create();
        $handlerStack->push(Middleware::retry(function ($retry, $request, $response, $exception) {
            // Retry on connection timeouts or server errors
            return $exception instanceof RequestException || ($response && $response->getStatusCode() >= 500);
        }));

        $this->baseUrl = config('jsconnector.api_url', 'http://localhost:3000');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'handler' => $handlerStack,
            'timeout' => config('jsconnector.timeout', 30),
        ]);
    }

    public function post($endpoint, $data)
    {
        return $this->request('POST', $endpoint, ['json' => $data]);
    }

    public function get($endpoint, $query = [])
    {
        return $this->request('GET', $endpoint, ['query' => $query]);
    }

    public function put($endpoint, $data)
    {
        return $this->request('PUT', $endpoint, ['json' => $data]);
    }

    public function delete($endpoint)
    {
        return $this->request('DELETE', $endpoint);
    }

    protected function request($method, $endpoint, $options = [])
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        Log::info('url: ' . $url);

        try {
            $response = $this->client->request($method, $url, $options);
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Server error. Please try again later.'], 500);
        }
    }
}
