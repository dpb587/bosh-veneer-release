<?php

namespace Veneer\HubBundle\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;

class Updater
{
    /**
     * @var HubFactory
     */
    protected $factory;

    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(HubFactory $factory, EntityManager $em)
    {
        $this->factory = $factory;
        $this->em = $em;
    }

    public function updateReleases($hubName, LoggerInterface $logger)
    {
        $hubService = $this->factory->get($hubName);
        $repository = $this->em->getRepository('VeneerHubBundle:ReleaseVersion');

        $this->em->beginTransaction();

        $i = 0;

        foreach ($hubService->yieldReleases() as $release) {
            $i += 1;

            $release->setHub($hubName);
            $release->setStatFirstSeenAt(new \DateTime());
            $release->setStatLastSeenAt($release->getStatFirstSeenAt());

            $found = $repository->findOneBy([
                'hub' => $release->getHub(),
                'release' => $release->getRelease(),
                'version' => $release->getVersion(),
            ]);

            if ($found) {
                $logger->debug(sprintf('release %s/%s (repeat)', $release->getRelease(), $release->getVersion()));

                $found->setDetailUrl($release->getDetailUrl());
                $found->setTarballUrl($release->getTarballUrl());
                $found->setTarballSize($release->getTarballSize());
                $found->setTarballChecksum($release->getTarballChecksum());
                $found->setStatLastSeenAt($release->getStatLastSeenAt());

                $release = $found;
            } else {
                $logger->info(sprintf('release %s/%s (new)', $release->getRelease(), $release->getVersion()));
            }

            NaiveSemverParser::parse($release);

            $this->em->persist($release);
            $this->em->flush();

            if (0 == $i % 50) {
                $this->em->clear();
            }
        }

        $this->em->commit();
    }

    public function updateStemcells($hubName, LoggerInterface $logger)
    {
        $hubService = $this->factory->get($hubName);
        $repository = $this->em->getRepository('VeneerHubBundle:StemcellVersion');

        $this->em->beginTransaction();

        $i = 0;

        foreach ($hubService->yieldStemcells() as $stemcell) {
            $i += 1;

            $stemcell->setHub($hubName);
            $stemcell->setStatFirstSeenAt(new \DateTime());
            $stemcell->setStatLastSeenAt($stemcell->getStatFirstSeenAt());

            $found = $repository->findOneBy([
                'hub' => $stemcell->getHub(),
                'stemcell' => $stemcell->getStemcell(),
                'version' => $stemcell->getVersion(),
            ]);

            if ($found) {
                $logger->debug(sprintf('stemcell %s/%s (repeat)', $stemcell->getStemcell(), $stemcell->getVersion()));

                $found->setSourceType($stemcell->getSourceType());
                $found->setDetailUrl($stemcell->getDetailUrl());
                $found->setTarballUrl($stemcell->getTarballUrl());
                $found->setTarballSize($stemcell->getTarballSize());
                $found->setTarballChecksum($stemcell->getTarballChecksum());
                $found->setStatLastSeenAt($stemcell->getStatLastSeenAt());

                $stemcell = $found;
            } else {
                $logger->info(sprintf('stemcell %s/%s (new)', $stemcell->getStemcell(), $stemcell->getVersion()));
            }

            NaiveSemverParser::parse($stemcell);

            $this->em->persist($stemcell);
            $this->em->flush();

            if (0 == $i % 50) {
                $this->em->clear();
            }
        }

        $this->em->commit();
    }
}
