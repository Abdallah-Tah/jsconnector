<?php

namespace Amohamed\JSConnector;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Artisan;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Process\Exception\ProcessFailedException;


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
        // C:\laragon\www\langchain-laravel\config\jsconnector.php
        $this->baseUrl = config('jsconnector.api_url', 'http://localhost:3000');
        Log::info('baseUrl: ' . $this->baseUrl);

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'handler' => $handlerStack,
            'timeout' => config('jsconnector.timeout', 30),
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
        // $this->startServerIfNotRunning();

        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        Log::info('url: ' . $url);
        Log::info('data: ' . json_encode($data));
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

    // //check if the command jsconnector is not running if not then run the command
    // protected function startServerIfNotRunning()
    // {
    //     $url = $this->baseUrl . '/healthcheck';

    //     try {
    //         $response = Http::get($url);

    //         if ($response->successful()) {
    //             Log::info('Server is running');
    //             return true;
    //         }
    //     } catch (\Exception $e) {
    //         if ($e instanceof \GuzzleHttp\Exception\ConnectException) {
    //             // If the server is down, start it
    //             $command = ['node', env('JSCONNECTOR_SERVER_PATH', 'resources/js/langchain.cjs')];
    //             $process = Process::fromShellCommandline(implode(' ', $command));

    //             try {
    //                 // Run the process in the background
    //                 $process->start();

    //                 // Immediately return, letting the request processing continue
    //                 return true;
    //             } catch (\Exception $exception) {
    //                 Log::error('Process failed. Error: ' . $exception->getMessage());

    //                 // Show error to user
    //                 return response()->json(['error' => 'Server could not be started.'], 500);
    //             }
    //         }
    //     }

    //     return false;
    // }

    // public function isServerRunning()
    // {
    //     $url = $this->baseUrl . '/healthcheck';

    //     try {
    //         $response = Http::get($url);

    //         if ($response->successful()) {
    //             return true;
    //         }
    //     } catch (\Exception $e) {
    //         // Log the error
    //         Log::error('Error checking server health: ' . $e->getMessage());
    //     }

    //     return false;
    // }
}
