<?php

namespace LaravelPackageManager\Support;

use Symfony\Component\Process\Process;
use LaravelPackageManager\Exceptions\InvalidExternalCommandException;

class RunExternalCommand
{

    protected $command;

    public function __construct($command)
    {
        $this->command = $command;
        $this->validateCommand();
    }

    public function run()
    {
        $process = new Process($this->command, base_path(), null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }

        $process->run(function ($type, $line) {
            $type = $type;
            $this->line($line);
        });
    }

    protected function validateCommand()
    {
        if (!is_string($this->command) || strlen(trim($this->command)) == 0)
            throw new InvalidExternalCommandException('Invalid command string.');

        return true;
    }

}
