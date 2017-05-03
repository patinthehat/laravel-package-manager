<?php

namespace LaravelPackageManager\Support;

class ItemInformation
{

    const UNDEFINED = 0;
    const UNKNOWN = 0;
    const FACADE = 1;
    const SERVICE_PROVIDER = 2;

    public $type = self::UNDEFINED;

    public $package = '';

    public $classname = '';

    public $extends = '';

    public $namespace = '';

    public $filename = '';

    public $name = '';

    public function __construct(int $type = self::UNDEFINED)
    {
        $this->type = $type;
    }

    /**
     * Convert a type into a string
     * @return string
     */
    public function displayName()
    {
        switch($this->type) {
            case self::SERVICE_PROVIDER:
                return 'Service Provier';
            case self::FACADE:
                return 'Facade';
            default:
                return "Unknown";
        }
    }

    /**
     * Get the fully-qualified classname
     *
     * @return string
     */
    public function qualifiedName()
    {
        return $this->namespace . '\\' . $this->classname;
    }

    public function __call($name, $params)
    {
        if (isset($this->$name)) {
            if (count($params)>0) {
                $this->$name = $params[0];
                return $this;
            }
            return $this->$name;
        }
    }

}
