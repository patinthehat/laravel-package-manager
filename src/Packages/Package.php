<?php

namespace LaravelPackageManager\Packages;


use LaravelPackageManager\Packages\Files\PackageFileLocator;
use LaravelPackageManager\Exceptions\InvalidPackageNameException;
use LaravelPackageManager\Exceptions\PackageDirectoryNotFoundException;

class Package
{
    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string|null
     */
    protected $version = null;

    /**
     * @var array
     */
    protected $files = [];

    /**
     *
     * @var \LaravelPackageManager\Packages\Files\PackageFileLocator
     */
    protected $locator;

    /**
     * Creates a package definition, with the optional 'packagename:^1.0' format; also validates the specified package name.
     * @param string $name
     * @throws \LaravelPackageManager\Exceptions\InvalidPackageNameException
     */
    public function __construct($name)
    {
        $this->name = strtolower(trim($name));
        if (strpos($this->name,':')!==false) {
            $parts = explode(':', $this->name);
            $this->name = $parts[0];
            $this->version = $parts[1];
        }

        $this->validateName();
        $this->locator = new PackageFileLocator($this);
    }

    /**
     * Check to see if the current package has already been installed.
     * @return boolean
     */
    public function isInstalled()
    {
        $packageInstalled = true;
        try {
            $this->validatePath();
        } catch (PackageDirectoryNotFoundException $e) {
            $packageInstalled = false;
        }

        return $packageInstalled;
    }

    /**
     * Validates a package name, expecting format "vendorname/packagename".
     * @throws \LaravelPackageManager\Exceptions\InvalidPackageNameException
     * @return boolean
     */
    protected function validateName()
    {
        $packageName = $this->getName();
        if (preg_match('/[a-zA-Z0-9_\-]+\/[a-zA-Z0-9_\-]+/', $packageName) == 0) {
            throw new InvalidPackageNameException("Invalid package name: $packageName");
            return false;
        }

        return true;
    }

    /**
     * Validate the path the package is expected to reside in.
     * @throws \LaravelPackageManager\Exceptions\PackageDirectoryNotFoundException
     * @return boolean
     */
    public function validatePath()
    {
        $path = $this->getPath();
        if (!is_dir($path)) {
            throw new PackageDirectoryNotFoundException("Package directory not found: $path");
            return false;
        }

        return true;
    }

    /**
     * The package name.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the specified version of the package to install.  Returns empty string if no version was explicitly specified.
     * @return string
     */
    public function getVersion()
    {
        return is_null($this->version) ? '' : $this->version;
    }

    /**
     * Returns the absolute path where this package will be installed.
     * @return string
     */
    public function getPath()
    {
        return base_path().'/vendor/'.$this->getName();
    }

}
