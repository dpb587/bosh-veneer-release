<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

// https://github.com/doctrine/dbal/pull/248
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\VarDateTimeType;
Type::overrideType('datetime', 'Doctrine\DBAL\Types\VarDateTimeType');

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),

            new Veneer\CoreBundle\VeneerCoreBundle(),
            new Veneer\BoshBundle\VeneerBoshBundle(),
            new Veneer\OpsBundle\VeneerOpsBundle(),
            new Veneer\AwsCpiBundle\VeneerAwsCpiBundle(),
//            new Veneer\LogsearchBundle\VeneerLogsearchBundle(),
            new Veneer\HubBundle\VeneerHubBundle(),
            new Veneer\WellnessBundle\VeneerWellnessBundle(),
            //new Veneer\CloqueBundle\VeneerCloqueBundle(),
            new Veneer\WardenCpiBundle\VeneerWardenCpiBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }
    
    public function getCacheDir()
    {
        return getenv('CACHEDIR') ?: parent::getCacheDir();
    }

    public function getLogDir()
    {
        return getenv('LOGDIR') ?: parent::getLogDir();
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(getenv('SYMFONY_PARAMS') ?: $this->getRootDir() . '/config/parameters.dist.yml');
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
