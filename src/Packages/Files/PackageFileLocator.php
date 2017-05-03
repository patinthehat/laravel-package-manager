<?php

namespace LaravelPackageManager\Packages\Files;

use LaravelPackageManager\Packages\Package;
use LaravelPackageManager\Packages\Files\PackageFileFilterIterator;
use LaravelPackageManager\Exceptions\PackageDirectoryNotFoundException;
use LaravelPackageManager\Packages\Files\PackageFileList;


/**
 * Handles the locating of files that might contain a service provider or facade.
 *
 */
class PackageFileLocator
{

    /**
     * @var \LaravelPackageManager\Packages\Package
     */
    protected $package;

    protected $files;

    public function __construct(Package $package)
    {
        $this->package = $package;
        $this->files = new PackageFileList;
    }

    /**
     * Recursively scan the package's directory for all files, using a filter
     * to strip out files we know won't have a service provider or Facade.
     * @param boolean $prioritization - prioritizes files that are most likely
     *  to contain SPs or Facades to the top of the resulting array.
     * @return \LaravelPackageManager\Packages\Files\PackageFileList
     */
    public function locateFiles($prioritization = true)
    {
        $fileiterator = new \RecursiveDirectoryIterator(
                            $this->package->getPath(),
                            \FilesystemIterator::KEY_AS_PATHNAME |
                            \FilesystemIterator::CURRENT_AS_FILEINFO |
                            \FilesystemIterator::SKIP_DOTS |
                            \FilesystemIterator::FOLLOW_SYMLINKS
                        );

        //loop through the file list, and apply a filter, removing files that we know
        //won't contain a Service Provider or Facade.
        $iterator = new \RecursiveIteratorIterator(
                        new PackageFileFilterIterator($fileiterator),
                        \RecursiveIteratorIterator::SELF_FIRST
                  );

        $result = [];

        //only allow php files with a filesize > 0
        //TODO Implement FilenameFilter class here
        foreach ($iterator as $file) {
            if (!$file->isDir() && $file->getExtension() == 'php' && $file->getSize() > 0)
                $result[] = $file;
        }


        if ($prioritization) {
            //sort the files, with files ending with "ServiceProvider" or "Facade" at the top,
            //to increase the speed at which we find those classes.
            usort($result, function($a,$b) {
                if (ends_with($a, 'ServiceProvider.php') || ends_with($a, 'Facade.php'))
                    return -1;
                if (ends_with($b, 'ServiceProvider.php') || ends_with($b, 'Facade.php'))
                    return 1;
                return strcmp($a,$b);
            });
        }

        $this->files->setFiles($result);

        return $this->getFiles();
    }

    public function getFiles()
    {
       return $this->files;
    }

    public function clearFiles()
    {
        $this->files = new PackageFileList;
        return $this;
    }

}
