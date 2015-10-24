<?php

namespace Veneer\WellnessBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Veneer\CoreBundle\DependencyInjection\CompilerPass\ContainerMapCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class VeneerWellnessBundle extends Bundle
{
    public function build(ContainerBuilder $builder)
    {
        parent::build($builder);

        $builder->addCompilerPass(new ContainerMapCompilerPass('veneer_wellness.check.action'));
        $builder->addCompilerPass(new ContainerMapCompilerPass('veneer_wellness.check.source'));
    }
}
