<?php

namespace LaravelPackageManager\Packages\Files\Scanner\Rules;

interface PackageFileScannerRuleContract
{
   /**
    * @param string $code
    * @return boolean
    */
   public function matchByFilename($filename);

   /**
    * @param string $code
    * @return boolean
    */
   public function matchBySourcecode($code);

   /**
    * @param string $code
    * @return \LaravelPackageManager\Support\ItemInformation
    */
   public function getInformationFromSourcecode($code);

   /**
    * @return integer
    */
   public function matchType();
}
