<?php

namespace Bosh\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Bosh\CoreBundle\DependencyInjection\CompilerPass\PluginCompilerPass;

class BoshCoreBundle extends Bundle
{
    public function build(ContainerBuilder $builder)
    {
        parent::build($builder);

        $builder->addCompilerPass(new PluginCompilerPass());
    }
}
