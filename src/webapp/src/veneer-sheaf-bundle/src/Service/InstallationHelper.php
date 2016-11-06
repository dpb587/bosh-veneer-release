<?php

namespace Veneer\SheafBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\Yaml\Yaml;
use Veneer\BoshEditorBundle\Service\ManifestBuilder\ManifestBuilderInterface;
use Veneer\CoreBundle\Service\Workspace\RepositoryInterface;

class InstallationHelper
{
    protected $em;
    protected $repository;
    protected $manifestBuilder;

    public function __construct(EntityManager $em, RepositoryInterface $repository, ManifestBuilderInterface $manifestBuilder)
    {
        $this->em = $em;
        $this->repository = $repository;
        $this->manifestBuilder = $manifestBuilder;
    }

    public function enumerateBoshDependencies(array $draftProfile, $path)
    {
        $sheafSpec = Yaml::parse($this->repository->showFile($path, $draftProfile['ref_read']));

        $releaseDependencies = [];
        $stemcellDependencies = [];

        $physicalCheckout = $this->repository->createCheckout($draftProfile['ref_read'])->getPhysicalCheckout();

        foreach ($sheafSpec['components'] as $component) {
            #if (!$sheafSpec['installation']['components'][$component['name']]['enabled']) continue;

            if ($component['type'] == 'deployment') {
                $result = Yaml::parse(
                    $this->manifestBuilder->build(
                        $physicalCheckout->getPhysicalPath(),
                        sprintf(
                            'bosh/deployment/%s-%s/manifest.yml',
                            $sheafSpec['installation']['name'],
                            $component['name']
                        )
                    )
                );

                if (isset($result['releases'])) {
                    foreach ($result['releases'] as $release) {
                        $installedRelease = $this->em->getRepository('VeneerBoshBundle:Releases')
                            ->createQueryBuilder('r')
                            ->andWhere(new Expr\Comparison('r.name', '=', ':release'))->setParameter('release', $release['name'])
                            ->getQuery()
                            ->getSingleResult();

                        if ($installedRelease) {
                            $installedVersion = $this->em->getRepository('VeneerBoshBundle:ReleaseVersions')
                                ->createQueryBuilder('v')
                                ->andWhere(new Expr\Comparison('v.release', '=', ':release'))->setParameter('release', $installedRelease)
                                ->andWhere(new Expr\Comparison('v.version', '=', ':version'))->setParameter('version', $release['version'])
                                ->getQuery()
                                ->getSingleResult();
                        } else {
                            $installedVersion = null;
                        }

                        $release['installed'] = (Boolean) $installedVersion;

                        $releaseDependencies[$release['name']] = $release;
                    }
                }
            }
        }

        return [
            'releases' => $releaseDependencies,
            'stemcells' => $stemcellDependencies,
        ];
    }
}
