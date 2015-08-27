<?php

namespace Veneer\TokensBundle\Service\Filter;

interface FilterInterface
{
    public function encode($value);
    public function decode($value);
}
