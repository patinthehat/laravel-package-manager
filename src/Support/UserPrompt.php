<?php

namespace LaravelPackageManager\Support;

use LaravelPackageManager\Support\ItemInformation;
use LaravelPackageManager\Support\Output;

class UserPrompt
{

    /**
     * @var \LaravelPackageManager\Support\Output
     */
    protected $output = null;

    /**
     * Constructor
     * @param Output $output
     * @return void
     */
    public function __construct(Output $output)
    {
        $this->output = $output;
    }

    /**
     * Ask the user if the specified item should be installed.
     * @param ItemInformation $info
     * @return void
     */
    public function promptToInstall(ItemInformation $info)
    {
        switch ($info->type) {
            case ItemInformation::FACADE:
            case ItemInformation::SERVICE_PROVIDER:
                if ($this->output->confirm("Register ".$info->displayName()." '".$info->name."'?")) {
                    $this->output->info('Registering '.$info->displayName().'...');
                    return true;
                }
                $this->output->comment('Skipping registration of '.$info->displayName().' \''.$info->name.'\'.');
        }

        return false;
    }

    /**
     * Ask the user if they want to unregister a service provider or facade.
     * @param \LaravelPackageManager\Support\ItemInformation $info
     * @return boolean
     */
    public function promptToUnregister(ItemInformation $info)
    {
        switch ($info->type) {
            case ItemInformation::FACADE:
            case ItemInformation::SERVICE_PROVIDER:
                if ($this->output->confirm("Unregister ".$info->displayName()." '".$info->name."'?")) {
                    $this->output->info('Unregistering '.$info->displayName().'...');
                    return true;
                }
                $this->output->comment('Skipping unregistration of '.$info->displayName().' \''.$info->name.'\'.');
        }

        return false;
    }

}
