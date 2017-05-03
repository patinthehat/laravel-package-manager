<?php

namespace LaravelPackageManager\Packages\Files\Scanner\Rules;

use LaravelPackageManager\Packages\Files\Scanner\Rules\PackageFileScannerRuleContract;
use LaravelPackageManager\Support\ItemInformation;
use LaravelPackageManager\Support\FacadeClassLoader;

class FacadeScannerRule implements PackageFileScannerRuleContract
{

    protected $facadeLoader = null;

    public function __construct()
    {
        $this->initFacadeLoader();
    }

    protected function initFacadeLoader()
    {
        $this->facadeLoader = new FacadeClassLoader;
        $this->facadeLoader->refresh();
    }

    /**
    * Match the classtype by filename.
    * @param string $filename
    * @return boolean
    */
    public function matchByFilename($filename)
    {
        return preg_match('/[a-zA-Z0-9_]*Facade\.php$/', basename($filename))==1;
    }

    /**
    * Match the classtype by scanning the source code.
    * @param string $code
    * @return boolean
    */
    public function matchBySourcecode($code)
    {
        return preg_match('/\b([a-zA-Z0-9_]+)\s+extends\s+([a-zA-Z0-9_\\\]*Facade)\b/', $code)==1;
    }

   /**
    * Extract information about this facade from its source code.
    * @param string $code
    * @return \LaravelPackageManager\Support\ItemInformation
    */
    public function getInformationFromSourcecode($code)
    {
        $ii = new ItemInformation;
        $ii->type(false);

        if (preg_match('/\b([a-zA-Z0-9_]+)\s+extends\s+([a-zA-Z0-9_\\\]*Facade)\b/', $code, $m)==1) {
            $this->initFacadeLoader();
            $facadeName = $this->facadeLoader->load($ii->filename, $code);//($code, true);

            $ii->classname($m[1])->extends($m[2])->type($this->matchType());
            $ii->name($facadeName !== false ? $facadeName : $m[1]);
        }

        $m = [];
        if (preg_match('/namespace ([^;]+)/', $code, $m)==1) {
            $ii->namespace($m[1]);
        }

        return $ii;
   }


    /**
    * The class type matched by this rule
    * @return integer
    */
    public function matchType()
    {
        return ItemInformation::FACADE;
    }
}
