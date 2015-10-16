<?php

namespace Veneer\TokensBundle\Service\Storage;

interface StorageInterface
{
    public function write($key, array $data);
    public function read($key);
    public function delete($key);
}
