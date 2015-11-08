<?php

namespace Veneer\OpsBundle\Service\Workspace\App;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Veneer\CoreBundle\Service\Workspace\App\AppInterface;
use Veneer\CoreBundle\Service\Workspace\Changeset;
use Veneer\CoreBundle\Service\Workspace\GitRepository;
use Symfony\Component\Yaml\Yaml;
use Veneer\OpsBundle\Entity\DeploymentWorkspace;
use Psr\Log\LoggerInterface;
use Veneer\CoreBundle\Service\Workspace\Checkout\CheckoutInterface;

class DeploymentApp implements AppInterface
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
    public function getAppTitle()
    {
        return 'Deployment Editor';
    }

    public function getAppDescription()
    {
        return 'Edit the various aspects of your deployment manifests';
    }

    public function getAppRoute()
    {
        return 'veneer_ops_workspace_app_deployment_summary';
    }

    public function onManifestCompile(CheckoutInterface $checkout, $path)
    {
        
    }

    public function onManifestCommit($branch, Changeset $changeset, $path)
    {
        $em = $this->container->get('doctrine.orm.state_entity_manager');

        $this->logger->debug($path);

        if (Changeset::DELETED == $changeset->getChange($path)) {
            if (null === $name = $this->getDeploymentName($changeset->showOldFile($path))) {
                $this->logger->debug('No deployment name in previous version');

                return;
            }

            if (null === $entity = $em->getRepository(DeploymentWorkspace::class)->find($name)) {
                $this->logger->debug('No deployment name in previous version');

                return;
            }

            $this->logger->info('Removed deployment "' . $name . '" reference');

            $em->remove($entity);
        } else {
            if (null === $name = $this->getDeploymentName($changeset->showNewFile($path))) {
                $this->logger->debug('No deployment name in new version');

                return;
            }

            if (null === $entity = $em->getRepository(DeploymentWorkspace::class)->find($name)) {
                $this->logger->info('New deployment "' . $name . '" reference');

                $entity = new DeploymentWorkspace();
                $entity->setDeployment($name);
            }

            $this->logger->info('Updated deployment "' . $name . '" reference');

            $entity->setSourcePath($path);

            $em->persist($entity);
        }

        $em->flush();
    }

    protected function getDeploymentName($data)
    {
        $match = null;

        if (!preg_match('/^(name:.+)$/m', $data, $match)) {
            return;
        }

        return current(Yaml::parse($match[1]));
    }
}
