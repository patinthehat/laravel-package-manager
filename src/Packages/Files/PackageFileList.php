<?php

namespace LaravelPackageManager\Packages\Files;

class PackageFileList implements \ArrayAccess, \Iterator
{
    protected $container = [];
    protected $position = 0;

    public function __construct()
    {
        //
    }

    public function getFiles()
    {
        return $this->container;
    }

    public function setFiles(array $files)
    {
        $this->container = $files;
    }

    public function add($file)
    {
        $this->container[] = $file;
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->container[] = $value;
            return;
        }
        $this->container[$offset] = $value;
    }

    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    public function rewind() {
        $this->position = 0;
    }

    public function current() {
        return $this->container[$this->position];
    }

    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        return isset($this->container[$this->position]);
    }
}
