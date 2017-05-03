<?php

namespace LaravelPackageManager\Support;

class CommandOptions
{
    protected $options = [];

    public function __construct(array $options)
    {
        if (count($options)>0)
            $this->options = $options;
    }

    /**
     * Add an option to the list
     * @param string $name
     */
    public function add($name)
    {
        array_push($this->options, $name);
    }

    /**
     * Remove an option.
     * @param string $name
     */
    public function remove($name)
    {
        unset($this->options[$name]);
    }

    /**
     * Remove all items from the list.
     */
    public function clear()
    {
        $this->options = [];
    }

    /**
     * Check if $name exists in options
     * @param string $name
     * @return boolean
     */
    public function hasOption($name)
    {
        return $this->has($name);
    }

    /**
     * Get the value of option $name.
     * @param string $name
     * @return mixed
     */
    public function option($name)
    {
        return $this->get($name);
    }

    /**
     * Check to see if $name exists in options
     * @param unknown $name
     * @return boolean
     */
    public function has($name)
    {
        return in_array($name, $this->options);
    }

    /**
     * Get the value of option $name
     * @param unknown $name
     * @return mixed
     */
    public function get($name)
    {
        if (is_null($this->options[$name])) {
            return false;
        }
        return $this->options[$name];
    }
}
