<?php

namespace Amohamed\JSConnector\Console\Commands;

use Illuminate\Console\Command;

class JsConnectorServeCommand extends Command
{
    protected $signature = 'jsconnector:serve';

    protected $description = 'Start the JSConnector Node.js server';

    public function handle()
    {
        $this->info('Starting JSConnector Node.js server...');

        $pathToServer = base_path(config('JSConnector.server_path'));
        chdir($pathToServer);


        $baseUrl = config('JSConnector.api_url');
        $command = "node server.js --baseUrl={$baseUrl}";

        exec($command, $output, $returnValue);

        if ($returnValue !== 0) {
            $this->error('Failed to start JSConnector Node.js server');
            return;
        }

        $this->info('JSConnector Node.js server started successfully');
    }
}
