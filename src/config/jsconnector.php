<?php

/**
 * Configuration file for JSConnector package.
 *
 * @package Laravel/JSConnector
 * @author Abdallah Mohamed
 */

 return [
    'api_url' => env('JSCONNECTOR_API_URL', 'http://localhost:3000'),
    'server_path' => env('JSCONNECTOR_SERVER_PATH', 'path/to/default/node/server'),
    'timeout' => env('JSCONNECTOR_TIMEOUT', 30),
];
