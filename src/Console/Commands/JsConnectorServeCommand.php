<?php

namespace Amohamed\JSConnector\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class JsConnectorServeCommand extends Command
{
    protected $signature = 'jsconnector:serve';

    protected $description = 'Start the JSConnector Node.js server';

    public function handle()
    {
        $this->info('Starting JSConnector Node.js server...');

        // Get the path to the server script
        $pathToServer = config('jsconnector.server_path');

        // Separate the directory and the filename
        $directory = dirname($pathToServer);
        $filename = basename($pathToServer);

        // Change the working directory
        chdir(base_path($directory));

        // Just run the Node.js script without any flags as the example node resources/js/langchain.cjs
        $command = 'node ' . $filename;
        Log::info('command: ' . $command);

        $descriptors = [
            ['pipe', 'r'],  // stdin
            ['file', storage_path('logs/jsconnector-serve-out.log'), 'w'],  // stdout
            ['file', storage_path('logs/jsconnector-serve-err.log'), 'w']   // stderr
        ];

        $process = proc_open($command, $descriptors, $pipes);

        if (is_resource($process)) {
            // Let's read from the stderr pipe
            $err = isset($pipes[2]) ? stream_get_contents($pipes[2]) : null;
            Log::info('err: ' . $err);
            if (!empty($err)) {
                $this->error("Error starting JSConnector Node.js server: $err");
            } else {
                $this->info('JSConnector Node.js server started successfully');
            }
            proc_close($process);
        } else {
            $this->error('Failed to start JSConnector Node.js server');
        }
    }
}
