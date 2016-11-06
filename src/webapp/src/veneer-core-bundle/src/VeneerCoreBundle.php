<?php

namespace Veneer\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Process\Process;

class VeneerCoreBundle extends Bundle
{
    public function build(ContainerBuilder $builder)
    {
        parent::build($builder);

        $builder->addCompilerPass(new DependencyInjection\CompilerPass\TwigStringCompilerPass());
        $builder->addCompilerPass(new DependencyInjection\CompilerPass\WorkspaceAppCompilerPass());
        $builder->addCompilerPass(new DependencyInjection\CompilerPass\WorkspaceLifecycleCompilerPass());
        $builder->addCompilerPass(new DependencyInjection\CompilerPass\ContainerMapCompilerPass('veneer_core.workspace.environment'));
        $builder->addCompilerPass(new DependencyInjection\CompilerPass\WorkspaceWatcherCompilerPass());
        $builder->addCompilerPass(new DependencyInjection\CompilerPass\RequestContextPluginCompilerPass());
        $builder->addCompilerPass(new DependencyInjection\CompilerPass\LinkProviderPluginCompilerPass());
        $builder->addCompilerPass(new DependencyInjection\CompilerPass\MetricSimpleContextCompilerPass());

        if (!$builder->hasParameter('veneer_core.build.tag')) {
            $p = new Process('git describe --exact-match --abbrev=0', __DIR__);
            $p->run();

            if (0 == $p->getExitCode()) {
                $builder->setParameter('veneer_core.build.tag', trim($p->getOutput()));
            } else {
                $builder->setParameter('veneer_core.build.tag', 'v0.0.0');
            }
        }

        if (!$builder->hasParameter('veneer_core.build.tag_commit')) {
            if ('v0.0.0' == $builder->getParameter('veneer_core.build.tag')) {
                $builder->setParameter('veneer_core.build.tag_commit', '0000000');
            } else {
                $p = new Process('git rev-parse --short '.escapeshellarg($builder->getParameter('veneer_core.build.tag')), __DIR__);
                $p->run();

                $builder->setParameter('veneer_core.build.tag_commit', trim($p->getOutput()));
            }
        }

        if (!$builder->hasParameter('veneer_core.build.commit')) {
            $p = new Process('git rev-parse --short HEAD', __DIR__);
            $p->run();

            if (0 == $p->getExitCode()) {
                $builder->setParameter('veneer_core.build.commit', trim($p->getOutput()), 'v');
            } else {
                $builder->setParameter('veneer_core.build.commit', '0000000');
            }
        }

        if (!$builder->hasParameter('veneer_core.build.dirty')) {
            $p = new Process('git diff-index --quiet HEAD --', __DIR__);
            $p->run();

            $builder->setParameter('veneer_core.build.dirty', $p->getExitCode() > 0);
        }
    }
}
