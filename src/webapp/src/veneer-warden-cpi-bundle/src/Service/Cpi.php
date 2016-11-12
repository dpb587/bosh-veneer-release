<?php

namespace Veneer\WardenCpiBundle\Service;

use Veneer\BoshBundle\Service\Cpi\CpiInterface;

class Cpi implements CpiInterface
{
    public function getName()
    {
        return 'warden';
    }

    public function getTitle()
    {
        return 'Warden/Garden';
    }

    public function getEditorFormType($name)
    {
        return sprintf('veneer_warden_cpi_editor_%s', $name);
    }

    public function supportsVipNetwork()
    {
        return false;
    }

    public function getControllerAction($object)
    {
        return sprintf('VeneerWardenCpiBundle:%s:cpi', $object);
    }
}
