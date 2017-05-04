<?php

namespace LaravelPackageManager\Console\Commands;

use Illuminate\Console\Command;
use LaravelPackageManager\Support\Output;
use LaravelPackageManager\Packages\Package;
use LaravelPackageManager\Support\UserPrompt;
use LaravelPackageManager\Packages\PackageRegistration;
use LaravelPackageManager\Packages\Files\PackageFileLocator;
use LaravelPackageManager\Packages\Files\PackageFileClassifier;

class UnregisterPackageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:unregister {package}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unregister service providers and facades from a previously registered package.';

    /**
     * The output helper.
     *
     * @var \LaravelPackageManager\Support\Output
     */
    protected $output;

    /**
     * The user prompt helper.
     *
     * @var \LaravelPackageManager\Support\UserPrompt
     */
    protected $userPrompt;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->output = new Output($this);
        $this->userPrompt = new UserPrompt($this->output);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $packageName = $this->argument('package');

        $package = new Package($packageName);

        $locator = new PackageFileLocator($package);
        $locator->locateFiles();

        $classifier = new PackageFileClassifier();
        $classifier->classifyFiles($locator->getFiles());

        $registrar = new PackageRegistration($this, $this->userPrompt);
        $registrar->unregisterAll($package, $classifier->getFiles());
    }
}
