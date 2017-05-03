<?php

namespace LaravelPackageManager\Support;

use Illuminate\Console\Command;

/**
 * Wrapper around the output commands of Illuminate\Console\Command, including 'info', 'comment', etc.
 */
class Output
{
    protected $command;
    
    public function __construct(Command $command)
    {
        $this->command = $command;
    }
    
    /**
     * Handle calls to various Command methods.
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public function __call($name, $params)
    {
        if (in_array($name, ['info', 'comment', 'line', 'error', 'confirm']))
            return $this->command->$name($params[0]);
    }
    
}
