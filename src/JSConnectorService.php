<?php

namespace Amohamed\JSConnector;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
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

        $this->baseUrl = config('JSConnector.api_url');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'handler' => $handlerStack,
            'timeout' => config('JSConnector.timeout', 30),
        ]);
    }

    public function post($endpoint, $data)
    {
        return $this->request('POST', $endpoint, $data);
    }

    public function get($endpoint, $query = [])
    {
        return $this->request('GET', $endpoint, [], $query);
    }

    public function put($endpoint, $data)
    {
        return $this->request('PUT', $endpoint, $data);
    }

    public function delete($endpoint)
    {
        return $this->request('DELETE', $endpoint);
    }

    protected function request($method, $endpoint, $data = [], $query = [])
    {
        $this->startServerIfNotRunning();

        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');

        if ($method === 'GET' && !empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->$method($url, $data);

            return $response->json();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Server error. Please try again later.'], 500);
        }
    }
}
