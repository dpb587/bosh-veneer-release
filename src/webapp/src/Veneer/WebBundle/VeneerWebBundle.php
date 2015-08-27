<?php

namespace Veneer\WebBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class VeneerWebBundle extends Bundle
{
    public function build(ContainerBuilder $builder)
    {
        parent::build($builder);

        $builder->addCompilerPass(new DependencyInjection\CompilerPass\RequestContextPluginCompilerPass());
        $builder->addCompilerPass(new DependencyInjection\CompilerPass\LinkProviderPluginCompilerPass());
        $builder->addCompilerPass(new DependencyInjection\CompilerPass\TopicProviderPluginCompilerPass());
    }
}
