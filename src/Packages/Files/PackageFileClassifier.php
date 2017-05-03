<?php

namespace LaravelPackageManager\Packages\Files;

use LaravelPackageManager\Support\ItemInformation;
use LaravelPackageManager\Packages\Files\PackageFileList;
use LaravelPackageManager\Packages\Files\Scanner\Rules\PackageFileScannerRuleContract;
use LaravelPackageManager\Packages\Files\Scanner\Rules\FacadeScannerRule;
use LaravelPackageManager\Packages\Files\Scanner\Rules\ServiceProviderScannerRule;

class PackageFileClassifier
{
    protected $files = [];

    /**
     * Classifies a file using the specified rule.
     * @param string $filename
     * @param PackageFileScannerRuleContract $rule
     * @return integer
     */
    public function classifyFile($filename, PackageFileScannerRuleContract $rule)
    {
        $data = file_get_contents($filename);

        $result = ($rule->matchByFilename($filename) || $rule->matchBySourcecode($data));
        if ($result)
            return $rule->matchType();

        return ItemInformation::UNKNOWN;
    }

    /**
     * Classifies multiple files.
     * @param PackageFileList $list
     * @return \LaravelPackageManager\Packages\Files\PackageFileClassifier
     */
    public function classifyFiles(PackageFileList $list)
    {
        foreach($list as $file) {
            $ii = new ItemInformation;
            $ii->filename = $file->getPathname();
            $rule = new FacadeScannerRule;
            $type = $this->classifyFile($ii->filename, $rule);

            if ($type == ItemInformation::UNKNOWN) {
                $rule = new ServiceProviderScannerRule;
                $type = $this->classifyFile($ii->filename, $rule);

                if ($type != ItemInformation::UNKNOWN) {
                    $ii = $rule->getInformationFromSourcecode(file_get_contents($file->getPathname()));
                    $ii->filename = $file->getPathname();
                    $ii->type = $type;
                }
            }

            if ($type != ItemInformation::UNKNOWN) {
                $ii = $rule->getInformationFromSourcecode(file_get_contents($file->getPathname()));
                $ii->filename = $file->getPathname();
                $ii->type = $rule->matchType();
            }

            if ($ii->type != ItemInformation::UNKNOWN)
                $this->files[$file->getPathname()] = $ii;
        }

        return $this;
    }

    /**
     * Returns the files property.
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

}
