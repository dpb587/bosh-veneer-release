<?php

namespace Veneer\AwsCpiBundle\Service;

use Veneer\BoshBundle\Service\Cpi\CpiInterface;

class Cpi implements CpiInterface
{
    public function getName()
    {
        return 'aws';
    }

    public function getTitle()
    {
        return 'Amazon Web Services';
    }

    public function getEditorFormType($name)
    {
        return sprintf('veneer_aws_cpi_editor_%s', $name);
    }
}