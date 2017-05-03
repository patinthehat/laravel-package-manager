<?php

namespace LaravelPackageManager\Packages;

use Illuminate\Console\Command;
use LaravelPackageManager\Packages\Package;
use LaravelPackageManager\Support\ItemInformation;
use LaravelPackageManager\Support\Output;
use LaravelPackageManager\Support\UserPrompt;
use LaravelPackageManager\Support\ConfigurationFile;

class PackageRegistration
{
    protected $command = null;
    protected $config = null;

    public function __construct(Command $command, UserPrompt $prompt)
    {
        $this->command = $command;
        $this->prompt = $prompt;
        $this->config = new ConfigurationFile('app');
    }

    /**
     * Register a facade or service provider within a package.
     * @param Package $package
     * @param ItemInformation $info
     */
    public function register(Package $package, ItemInformation $info)
    {
        $package = $package;
        $searchLine = null;
        switch($info->type) {
            case ItemInformation::FACADE:
                $searchLine = "'aliases' => [";

            case ItemInformation::SERVICE_PROVIDER:
                if (strlen($searchLine)==0)
                    $searchLine = 'LaravelPackageManager\\LaravelPackageManagerServiceProvider::class,';

                $install = $this->prompt->promptToInstall($info);
                break;

            default:
                $this->command->comment('Unknown package type.  This is probably not a Laravel package.');
                return false;
        }

        if ($install) {
            $regline = $this->generateRegistrationLine($info);

            $config = $this->config->read();
            if (strpos($config, $regline)!==false) {
                $this->command->comment($info->displayName() ."'".$info->name."' is already registered.");
                return true;
            }

            $count = 0;
            $config = str_replace($searchLine, $searchLine . PHP_EOL . "        $regline", $config, $count);

            if ($count > 0) {
                $this->config->write($config);
                return true;
            }
        }

        return false;
    }

    public function unregister(Package $package, ItemInformation $info)
    {
        $package = $package;
        $searchLine = null;
        switch($info->type) {
            case ItemInformation::FACADE:
            case ItemInformation::SERVICE_PROVIDER:
                $unregister = $this->prompt->promptToUnregister($info);
                break;

            default:
                $this->command->comment('Unknown package type.  This is probably not a Laravel package.');
                return false;
        }

        if ($unregister) {
            $regline = $this->generateRegistrationLine($info);

            $config = $this->config->read();
            if (strpos($config, $regline)===false) {
                $this->command->comment($info->displayName() ."'".$info->name."' is not registered.");
                return false;
            }

            $count = 0;
            $config = str_replace($regline, '', $config, $count);

            if ($count > 0) {
                $this->config->write($config);
                return true;
            }
        }

        return false;
    }

    /**
     * Register mutliple facades/service providers.
     * @param Package $package
     * @param array $infos
     */
    public function registerAll(Package $package, array $infos)
    {
        $result = true;
        foreach($infos as $info) {
            if (!$this->register($package, $info)) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Unregister mutliple facades/service providers.
     * @param Package $package
     * @param array $infos
     */
    public function unregisterAll(Package $package, array $infos)
    {
        $result = true;
        foreach($infos as $info) {
            if (!$this->unregister($package, $info)) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Generate the code needed to register the service provider or facade.
     *
     * @param ItemInformation $item
     * @return string
     */
    protected function generateRegistrationLine(ItemInformation $item)
    {
        switch ($item->type) {
            case ItemInformation::SERVICE_PROVIDER:
                return $item->namespace . "\\" . $item->classname . "::class,";

            case ItemInformation::FACADE:
                return "'" . (strlen($item->name) > 0 ? $item->name : $item->classname) . "' => " . $item->namespace . '\\' . $item->classname . "::class,";

            default:
                return '';
        }
    }

}

