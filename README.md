# Laravel JS Connector

The Laravel JS Connector package provides an easy way to integrate the JSConnector JavaScript library into your Laravel application. This package offers a simple facade to interact with the JSConnector library, enabling developers to leverage the power of OpenAI's language models in their Laravel projects without the hassle of directly managing communication between PHP and JavaScript.

## Installation

You can install the package via composer:

```bash
composer require amohamed/jsconnector
```

## Configuration

Publish the config file with:

```bash
php artisan vendor:publish --provider="Amohamed\JSConnector\JSConnectorServiceProvider" --tag="config"
```

This is the contents of the config file:

```php
return [
    'api_url' => env('JS_CONNECTOR_API_URL', 'http://localhost:3000/api'),
    'retry_times' => env('JS_CONNECTOR_RETRY_TIMES', 3),
    'retry_interval' => env('JS_CONNECTOR_RETRY_INTERVAL', 100),
];
```

You can customize the values in the .env file like so:

```bash
JS_CONNECTOR_API_URL=http://localhost:3000/api
JS_CONNECTOR_RETRY_TIMES=3
JS_CONNECTOR_RETRY_INTERVAL=100
```

## Starting and Stopping the Node.js Server

Before you can start making requests with JSConnector, you need to ensure that the Node.js server is running. You can start the server with the provided artisan command:

```bash
php artisan jsconnector:serve
```

To stop the running server, you can use:

```bash
php artisan jsconnector:stop
```

## Usage

Here is a basic example of how to use the JS Connector:

```php
$response = JSConnector::post('test-endpoint', ['baz' => 'qux']);
```

## Usage with LangChain JS

To use JSConnector with LangChain JS in your Laravel application, you need to install LangChain JS first. You can do this by running:

```bash
npm install -S langchain
```

Then, on your Node.js server, you can create a JavaScript file where you import and initialize LangChain:

```javascript
require('dotenv').config();

const { OpenAI } = require('langchain/llms/openai');
const { BufferMemory } = require('langchain/memory');
const { ConversationChain } = require('langchain/chains');

const model = new OpenAI({ key: process.env.OPENAI_API_KEY });
const memory = new BufferMemory();
const chain = new ConversationChain({ llm: model, memory: memory });
const cors = require('cors');

const express = require('express');
const app = express();
app.use(cors());

app.use(express.json());

app.post('/chat', async (req, res) => {
    console.log(`Request body: ${JSON.stringify(req.body)}`);
    const result = await chain.call({ input: req.body.input });
    console.log(`API response: ${JSON.stringify(result)}`);
    res.send(result);
});

app.listen(3000, () => {
    console.log('Langchain server running on port 3000');
});
```

In your Laravel application, you can use the JSConnector to send data to the LangChain JS service:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Amohamed\JSConnector\Facades\JSConnector;

class LangChainController extends Controller
{
    public function chat(Request $request)
    {
        $input = $request->input('message');

        // We use the post method on the JSConnector facade
        $response = JSConnector::post('chat', ['input' => $input]);

        // Then we return the response from the langchainjs service
        return response()->json(['response' => $response]);
    }
}

```

`Route::post('/chat', 'App\Http\Controllers\LangChainController@chat');`

This will send the response from the LangChain JS service back to the client.

## Testing

Run the tests with:

```bash
vendor/bin/phpunit
```

## License

The Laravel JS Connector is open-sourced software licensed under the MIT license.

## Authors

Abdallah Mohamed (<abdal_cascad@hotmail.com>)