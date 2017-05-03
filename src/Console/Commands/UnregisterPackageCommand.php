<?php

namespace LaravelPackageManager\Console\Commands;

use Illuminate\Console\Command;
use LaravelPackageManager\Packages\Package;
use LaravelPackageManager\Packages\PackageInstaller;
use LaravelPackageManager\Packages\Files\PackageFileLocator;
use LaravelPackageManager\Packages\Files\PackageFileClassifier;
use LaravelPackageManager\Support\ItemInformation;
use LaravelPackageManager\Packages\Files\Scanner\Rules\FacadeScannerRule;
use LaravelPackageManager\Packages\PackageRegistration;
use LaravelPackageManager\Support\Output;
use LaravelPackageManager\Support\UserPrompt;
use LaravelPackageManager\Support\CommandOptions;

class UnregisterPackageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:unregister {package_name} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unregister service providers and facades from a previously registered package.';

    protected $output;

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
        $packageName = $this->argument('package_name');

        $package = new Package($packageName);

        $locator = new PackageFileLocator($package);
        $locator->locateFiles();

        $classifier = new PackageFileClassifier();
        $classifier->classifyFiles($locator->getFiles());

        $registrar = new PackageRegistration($this, $this->userPrompt);
        $registrar->unregisterAll($package, $classifier->getFiles());
    }
}
