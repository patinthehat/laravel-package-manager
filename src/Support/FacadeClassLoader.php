<?php

namespace LaravelPackageManager\Support;

/**
 * Load a file that contains a laravel facade, and replace the classname with a random
 * classname, then load the resulting code and call the getFacadeAccessor() method to
 * reliably determine the Facade's actual name.
 *
 */
class FacadeClassLoader
{
    /**
     * @var array
     */
    protected $tempFiles = [];

    /**
     * The main method for this class.  Load a processed version of the Facade file,
     * extracting the actual name from the code.
     * Returns false on error.
     * @param string $filename
     * @param string $isContent
     * @return boolean|string
     */
    //public function load($filename, $isContent = false)
    public function load($filename, $content = null)
    {
        switch(is_string($content)) {
            case true:
                $contents = $content;
                break;
            default:
                if (!file_exists($filename)) {
                    return false;
                }
        }

        if (strlen($contents)==0)
            $contents = file_get_contents($filename);

        $loaderClassname = $this->getLoaderClassname();
        $loaderfn = $this->getLoaderFilename($loaderClassname);
        $contents = $this->processFacadeFileContents($loaderClassname, $contents);

        $this->writeDataToTempFile($loaderfn, $contents);

        try {
            include_once($loaderfn);
            $facadeName = $loaderClassname::getFacadeName();
        } catch(\Exception $e) {
            return false;
        } finally {
            $this->cleanup();
        }

        return ucfirst($facadeName);
    }


    /**
     * Refreshes the class loader.  Required when using the same class loader multiple times,
     * to avoid returning the wrong classname.  If you are repeatedly getting the same
     * classname for different Facade files, call refresh().
     * @return \LaravelPackageManager\Support\FacadeClassLoader
     */
    public function refresh()
    {
        $this->getLoaderClassname(true);
        return $this;
    }

    /**
     * Write data to a temp file
     * @param string $filename
     * @param string $data
     * @return \LaravelPackageManager\Support\FacadeClassLoader
     */
    protected function writeDataToTempFile($filename, $data)
    {
        if (!in_array($filename, $this->tempFiles))
            $this->tempFiles[] = $filename;
        file_put_contents($filename, $data);

        return $this;
    }

    /**
     * Process the contents of the original Facade class, removing namespace &
     * uses.  Renames the existing classname with a dynamic
     * one. Makes all methods public, rename getFacadeAccessor()
     * to getFacadeName().
     * @param string $loaderClassname
     * @param string $contents
     * @return string
     */
    protected function processFacadeFileContents($loaderClassname, $contents)
    {
        $temp = $contents;
        $temp = str_replace('namespace ', '//namespace ', $temp);
        $temp = preg_replace('/use /', '//use ', $temp);
        $temp = preg_replace('/class\s+([a-zA-Z0-9_]+)\s+extends\s+[\\\A-Za-z0-9_]*Facade/', "class $loaderClassname ", $temp);
        $temp = preg_replace('/(protected|private)/', 'public', $temp);
        $temp = preg_replace('/getFacadeAccessor/', 'getFacadeName', $temp);
        //$temp = preg_replace('/protected\s+static\s+function\s+getFacadeAccessor\b/', 'public static function getFacadeName', $temp);

        return $temp;
    }

    /**
     * Stores the dynamically generated temp classname for use with loading
     * the Facade code.
     * @param string $refresh
     * @return string
     */
    protected function getLoaderClassname($refresh = false)
    {
        static $classname = '';
        if ($classname == '' || $refresh === true)
            $classname = (new \ReflectionClass($this))->getShortName().sha1(mt_rand(1, 999999999));

        return $classname;
    }

    /**
     * Return the dynamically generated filename for loading the Facade code.
     * @param string $classname
     * @return string
     */
    protected function getLoaderFilename($classname)
    {
        return $classname.".laravel-require.facade-loader.php";
    }

    /**
     * Remove the temp file that was used to load the facade code.
     * @return \LaravelRequire\Support\FacadeClassLoader
     */
    protected function cleanup()
    {
        foreach($this->tempFiles as $file) {
            if (file_exists($file))
               unlink($file);
        }
        $this->tempFiles = [];
        return $this;
    }

}
