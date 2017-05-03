<?php

namespace LaravelPackageManager\Packages;

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

class PackageRequirer
{
    /**
     *
     * @var \LaravelPackageManager\Support\Output
     */
    protected $output;

    /**
     * @var \LaravelPackageManager\Support\UserPrompt
     */
    protected $userPrompt;

    /**
     * @var \LaravelPackageManager\Support\CommandOptions
     */
    protected $options;

    /**
     * @var \Illuminate\Console\Command
     */
    protected $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
        $this->output = new Output($this->command);
        $this->userPrompt = new UserPrompt($this->output);
        $this->options = new CommandOptions([]);
    }

    /**
     * Install a package, and register any service providers and/or facades it provides.
     * @param string $packageName
     * @param array $options
     */
    public function require($packageName, array $options)
    {
        $package = new Package($packageName);
        if (!$package->isInstalled() || !$options['register-only']) { // (is_null($options['register-only']) || $options['register-only'] == false)) {
            $installer = new PackageInstaller;
            $installer->install($package, $this->options->hasOption('dev')); // in_array('dev', $options));
        }

        $locator = new PackageFileLocator($package);
        $locator->locateFiles();

        $classifier = new PackageFileClassifier();
        $classifier->classifyFiles($locator->getFiles());

        $registrar = new PackageRegistration($this->command, $this->userPrompt);
        $registrar->registerAll($package, $classifier->getFiles());
    }
}
