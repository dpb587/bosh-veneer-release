<?php

namespace Veneer\TokensBundle\Service\Storage;

use Symfony\Component\Yaml\Yaml;

class YamlStorage implements StorageInterface
{
    protected $path;
    protected $options = [];

    public function __construct($path, array $options = [])
    {
        $this->path = $path;
        $this->options = $options;
    }

    public function write($key, array $data)
    {

    }

    public function read($key)
    {

    }

    public function delete($key)
    {

    }

    private function import()
    {
        return Yaml::parse(file_get_contents($this->path));
    }

    private function export()
    {
        file_put_contents
    }
}
