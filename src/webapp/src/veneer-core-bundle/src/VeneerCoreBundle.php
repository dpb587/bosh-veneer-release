<?php

namespace Veneer\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class VeneerCoreBundle extends Bundle
{
    public function build(ContainerBuilder $builder)
    {
        parent::build($builder);

        $builder->addCompilerPass(new DependencyInjection\CompilerPass\WorkspaceEditorCompilerPass());
        $builder->addCompilerPass(new DependencyInjection\CompilerPass\WorkspaceWatcherCompilerPass());
        $builder->addCompilerPass(new DependencyInjection\CompilerPass\RequestContextPluginCompilerPass());
        $builder->addCompilerPass(new DependencyInjection\CompilerPass\LinkProviderPluginCompilerPass());
        $builder->addCompilerPass(new DependencyInjection\CompilerPass\TopicProviderPluginCompilerPass());
    }
}
