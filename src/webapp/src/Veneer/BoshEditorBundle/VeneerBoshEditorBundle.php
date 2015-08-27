<?php

namespace Veneer\BoshEditorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Veneer\BoshBundle\DependencyInjection\CompilerPass\PluginCompilerPass;
use Veneer\BoshBundle\DependencyInjection\Security\Factory\BoshDirectorFactory;

class VeneerBoshEditorBundle extends Bundle
{
    public function build(ContainerBuilder $builder)
    {
        parent::build($builder);

        $extension = $builder->getExtension('security');
        $extension->addSecurityListenerFactory(new BoshDirectorFactory());

        $builder->addCompilerPass(new PluginCompilerPass());
    }
}
