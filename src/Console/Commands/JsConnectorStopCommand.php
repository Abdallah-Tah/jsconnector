<?php

namespace Amohamed\JSConnector\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;

class JsConnectorStopCommand extends Command
{
    protected $signature = 'jsconnector:stop';

    protected $description = 'Stop the JSConnector Node.js server';

    public function handle()
    {
        $this->info('Stopping JSConnector Node.js server...');

        // Read the PID from the file
        $pidFile = storage_path('jsconnector.pid');
        if (!Storage::exists($pidFile)) {
            $this->error('JSConnector Node.js server is not running.');
            return;
        }

        $pid = Storage::get($pidFile);

        // Send a SIGTERM signal to the process
        $process = Process::fromShellCommandline("kill -9 {$pid}");

        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Remove the PID file
        Storage::delete($pidFile);

        $this->info('JSConnector Node.js server stopped successfully.');
    }
}
