<?php

namespace Veneer\OpsBundle\Service\Workspace\Lifecycle;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Veneer\CoreBundle\Service\Workspace\GitRepository;
use Symfony\Component\Yaml\Yaml;
use Veneer\CoreBundle\Entity\WorkspaceDefinition;
use Psr\Log\LoggerInterface;
use Veneer\CoreBundle\Service\Workspace\Checkout\CheckoutInterface;
use Veneer\CoreBundle\Service\Workspace\Lifecycle\LifecycleInterface;
use Veneer\CoreBundle\Service\Workspace\Lifecycle\Plan\Plan;
use Veneer\CoreBundle\Service\Workspace\Environment\EnvironmentContext;

class ManifestLifecycle implements LifecycleInterface
{
    protected $repository;
    protected $container;
    protected $logger;

    public function __construct(GitRepository $repository, ContainerInterface $container, LoggerInterface $logger)
    {
        $this->repository = $repository;
        $this->container = $container;
        $this->logger = $logger;
    }

    public function onPlan(CheckoutInterface $base, CheckoutInterface $changed, $path)
    {
        $compiledPath = $this->getCompiledPath($path);

        $changedData = $changed->get($compiledPath);
        $baseData = $base->get($compiledPath);

        if ($changedData == $baseData) {
            return;
        }

        $plan = $this->createPlan(Yaml::parse($baseData), Yaml::parse($changedData));

        if (!$plan->getDetails()) {
            return;
        }

        return $plan;
    }

    protected function createPlan(array $baseManifest, array $changedManifest)
    {
        $plan = new Plan();

        $this->createPlanProperties($plan, $baseManifest, $changedManifest);

        return $plan;

        $baseManifest = $this->buildDiffIndexManifest($baseManifest);
        $changedManifest = $this->buildDiffIndexManifest($changedManifest);

        // releases

        foreach ($baseManifest['releases'] as $releaseName => $release) {
            if (!isset($changedManifest['releases'])) {
                $plan->addDetail('release', 'remove', $releaseName);
            } elseif ($release != $changedManifest['releases'][$releaseName]) {
                $plan->addDetail(
                    'release',
                    (0 > version_compare($release['version'], $changedManifest['releases'][$releaseName]['version'])) ? 'upgrade' : 'downgrade',
                    $releaseName,
                    'to '.$changedManifest['releases'][$releaseName]['version']
                );
            }

            unset($changedManifest['releases'][$releaseName]);
        }

        foreach ($changedManifest['releases'] as $releaseName => $release) {
            $plan->addDetail('release', 'add', $releaseName);
        }

        // properties

        $this->createPlanProperties(
            $plan,
            isset($baseManifest['properties']) ? $baseManifest['properties'] : [],
            isset($changedManifest['properties']) ? $changedManifest['properties'] : []
        );

        return $plan;
    }

    protected function buildDiffIndexManifest(array $manifest)
    {
        foreach (['jobs', 'networks', 'releases', 'resource_pools'] as $key) {
            if (isset($manifest[$key])) {
                $manifest[$key] = array_combine(
                    array_map(
                        function (array $v) {
                            return $v['name'];
                        },
                        $manifest[$key]
                    ),
                    $manifest[$key]
                );
            } else {
                $manifest[$key] = [];
            }
        }

        return $manifest;
    }

    protected function createPlanProperties(Plan $plan, array $base, array $changed, $path = '')
    {
        foreach ($base as $key => $value) {
            if (!isset($changed[$key])) {
                $plan->addDetail('property', 'remove', ltrim($path.'.'.$key, '.'));
            } elseif ($changed[$key] != $value) {
                if (is_array($value) && is_string(key($value))) {
                    $this->createPlanProperties($plan, $value, $changed[$key], ltrim($path.'.'.$key, '.'));
                } else {
                    $plan->addDetail('property', 'change', ltrim($path.'.'.$key, '.'));
                }
            }

            unset($changed[$key]);
        }

        foreach ($changed as $key => $value) {
            $plan->addDetail('property', 'add', ltrim($path.'.'.$key, '.'));
        }
    }

    public function onApply(CheckoutInterface $checkout, $path, $monitorUrl = null)
    {
        $manifest = $checkout->get($this->getCompiledPath($path));
        $manifestData = Yaml::parse($manifest);

        // bosh deploy
        $task = $this->container->get('veneer_bosh.director.client')->sendForTask(
            'PUT',
            sprintf('deployment/%s', $manifestData['name']),
            $manifest
        );

        $router = $this->container->get('router');

        return $router->generate(
            'veneer_bosh_task_summary',
            [
                'task_id' => $task,
                'continue_to' => $monitorUrl,
            ]
        );
    }

    public function onRefresh(CheckoutInterface $checkout, $path)
    {
        // bosh keeps the state, so we don't use this here
        return;
    }

    public function onCommit(CheckoutInterface $checkout, $path)
    {
        $manifest = $checkout->get($path);

        if (!preg_match('/^(name:.+)$/m', $manifest, $match)) {
            return;
        }

        $name = current(Yaml::parse($match[1]));

        $def = new WorkspaceDefinition();
        $def->setPath($path);
        $def->setDefinitionType('bosh/deployment');
        $def->setDefinitionName($name);

        $em = $this->container->get('doctrine.orm.state_entity_manager');
        $em->persist($def);
        $em->flush();
    }

    public function onCompile(CheckoutInterface $checkout, $path)
    {
        $manifest = Yaml::parse($checkout->get($path));

        $env = new EnvironmentContext(
            $checkout,
            $this->container->get('veneer_core.workspace.environment'),
            $path,
            'compile'
        );

        $twig = $this->container->get('twig_string');

        $compiled = $this->compileHash($manifest, $twig, $env);

        $checkout->put(
            $this->getCompiledPath($path),
            Yaml::dump($compiled, 8),
            0600
        );

        return;
    }

    protected function compileHash(array $manifest, \Twig_Environment $twig, EnvironmentContext $env)
    {
        foreach ($manifest as $key => $value) {
            if (is_string($value)) {
                if (false !== strpos($value, '{{')) {
                    $manifest[$key] = $twig->render($value, ['env' => $env]);
                }
            } elseif (is_array($value)) {
                $manifest[$key] = $this->compileHash($value, $twig, $env);
            }
        }

        return $manifest;
    }

    protected function getCompiledPath($path)
    {
        return dirname($path).'/.'.basename($path);
    }
}
