<?php

namespace Veneer\OpsBundle\Service;

use Veneer\CoreBundle\Service\Workspace\Changeset;
use Veneer\CoreBundle\Service\Workspace\GitRepository;
use Veneer\OpsBundle\Entity\DeploymentWorkspace;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

class WorkspaceWatcher
{
    protected $repository;
    protected $container;

    public function __construct(GitRepository $repository, ContainerInterface $container)
    {
        $this->repository = $repository;
        $this->container = $container;
    }

    public function onDeploymentManifest($branch, Changeset $changeset, $path)
    {
        $em = $this->container->get('doctrine.orm.state_entity_manager');

        if (Changeset::DELETED == $changeset->getChange($path)) {
            if (null === $name = $this->getDeploymentName($changeset->showOldFile($path))) {
                return;
            }

            if (null === $entity = $em->getRepository(DeploymentWorkspace::class)->find($name)) {
                return;
            }

            $em->remove($entity);
            $em->flush();
        } else {
            if (null === $name = $this->getDeploymentName($changeset->showNewFile($path))) {
                return;
            }

            if (null === $entity = $em->getRepository(DeploymentWorkspace::class)->find($name)) {
                $entity = new DeploymentWorkspace();
                $entity->setDeployment($name);
            }

            $entity->setSourcePath($path);

            $em->persist($entity);
            $em->flush();
        }
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
