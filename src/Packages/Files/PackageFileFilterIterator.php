<?php

namespace LaravelPackageManager\Packages\Files;

use LaravelPackageManager\Support\RegEx;

class PackageFileFilterIterator extends \RecursiveFilterIterator
{

    /**
     *
     * @var string
     */
    public static $compiledRejectFilter = '';

    /**
     *
     * @var string
     */
    public static $rejectFilters = array(
        '/(CHANGES|LICENSE|README|VERSION)/',
        '/(tests|test|views|node_modules)/',
        '/^\.git(ignore|keep|attributes)?/',
        '/.*\.(exe|zip|tar|gz|bz2|xml|md|js|json|yml|txt|js|php_cs|editorconfig|dist)$/',
        '/[A-Za-z0-9_]+(Interface|Exception|Test|\.blade)\.php$/'
    );

    /**
     *
     * @var string
     */
    public static $compiledAcceptFilter = '';

    /**
     *
     * @var array
     */
    public static $acceptFilters = [
        '/src/',
        '/\.php/'
    ];

    /**
     *
     * @var \LaravelPackageManager\Support\Regex
     */
    protected $regex;

    public function __construct(\RecursiveIterator $iterator)
    {
        parent::__construct($iterator);

        $this->regex = new RegEx();
        // compile all of the filters into one regular expression
        if (self::$compiledRejectFilter == '')
            self::$compiledRejectFilter = $this->regex->compileFiltersToRegEx(self::$rejectFilters);

        if (self::$compiledAcceptFilter == '')
            self::$compiledAcceptFilter = $this->regex->compileFiltersToRegEx(self::$acceptFilters);
    }

    protected function matches($filename, $filter)
    {
        if ($this->regex->isRegularExpression($filter))
            return (preg_match($filter, $filename) == 1); // filter is a regular
                                                        // expression
        return ($filename == $filter); // filter requires an exact match
    }

    /**
     * Accept or reject the current file based on the regular expression
     * generated
     * upon class creation.
     *
     * @return boolean
     */
    public function accept()
    {
        $filename = $this->current()->getFilename();

        return !$this->matches($filename, self::$compiledRejectFilter);
    }

}