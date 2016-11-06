<?php

namespace Veneer\BoshBundle\Service\Cpi;

interface CpiInterface
{
    public function getName();
    public function getTitle();
    public function getEditorFormType($name);
}
