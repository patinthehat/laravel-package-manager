<?php

namespace LaravelPackageManager\Packages\Files\Scanner\Rules;

use LaravelPackageManager\Packages\Files\Scanner\Rules\PackageFileScannerRuleContract;
use LaravelPackageManager\Support\ItemInformation;

class ServiceProviderScannerRule implements PackageFileScannerRuleContract
{

    /**
    * Match the classtype by filename.
    * @param string $filename
    * @return boolean
    */
    public function matchByFilename($filename)
    {
        return preg_match('/[a-zA-Z0-9_]*ServiceProvider\.php/', basename($filename))==1;
    }

    /**
    * Match the classtype by scanning the source code.
    * @param string $code
    * @return boolean
    */
    public function matchBySourcecode($code)
    {
        return preg_match('/([a-zA-Z0-9_]+)\s+extends\s+([a-zA-Z0-9_\\\]*ServiceProvider)/', $code)==1;
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

        if (preg_match('/([a-zA-Z0-9_]+)\s+extends\s+([a-zA-Z0-9_\\\]*ServiceProvider)/', $code, $m)==1) {
            $ii->classname($m[1])->extends($m[2])->name($m[1])->type($this->matchType());
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
        return ItemInformation::SERVICE_PROVIDER;
    }

}
