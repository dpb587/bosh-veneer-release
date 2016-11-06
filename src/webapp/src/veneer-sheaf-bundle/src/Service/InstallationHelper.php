<?php

namespace Veneer\SheafBundle\Service;

use Doctrine\ORM\EntityManager;
use Veneer\OpsBundle\Service\ManifestBuilder\ManifestBuilderInterface;

class InstallationHelper
{
    protected $em;
    protected $manifestBuilder;

    public function __construct(EntityManager $em, ManifestBuilderInterface $manifestBuilder)
    {
        $this->em = $em;
        $this->manifestBuilder = $manifestBuilder;
    }
}