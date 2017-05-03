<?php

namespace LaravelPackageManager\Console\Commands;

use Illuminate\Console\Command;
use LaravelPackageManager\Packages\PackageRequirer;

class RequirePackageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:require {package_name} '.
                            '{--r | --register-only : skip installing package with composer} '.
                            '{--d | --dev : install package in development dependencies}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install a package and automatically register its service providers and facades.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $options = [
            'register-only'=>($this->hasOption('register-only') ? $this->option('register-only') : null),
            'dev' => ($this->hasOption('dev') ? $this->option('dev') : null),
        ];

        $requirer = new PackageRequirer($this);
        $requirer->require($this->argument('package_name'), $options);
    }
}
