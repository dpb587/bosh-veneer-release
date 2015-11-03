<?php

namespace Veneer\MarketplaceBundle\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;
use Veneer\MarketplaceBundle\Service\NaiveSemverParser;

class Updater
{
    /**
     * @var MarketplaceFactory
     */
    protected $factory;

    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(MarketplaceFactory $factory, EntityManager $em)
    {
        $this->factory = $factory;
        $this->em = $em;
    }

    public function updateReleases($marketplaceName, LoggerInterface $logger)
    {
        $marketplaceService = $this->factory->get($marketplaceName);
        $repository = $this->em->getRepository('VeneerMarketplaceBundle:ReleaseVersion');

        $this->em->beginTransaction();

        $i = 0;

        foreach ($marketplaceService->yieldReleases() as $release) {
            $i += 1;

            $release->setMarketplace($marketplaceName);
            $release->setStatFirstSeenAt(new \DateTime());
            $release->setStatLastSeenAt($release->getStatFirstSeenAt());

            $found = $repository->findOneBy([
                'marketplace' => $release->getMarketplace(),
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

    public function updateStemcells($marketplaceName, LoggerInterface $logger)
    {
        $marketplaceService = $this->factory->get($marketplaceName);
        $repository = $this->em->getRepository('VeneerMarketplaceBundle:StemcellVersion');

        $this->em->beginTransaction();

        $i = 0;

        foreach ($marketplaceService->yieldStemcells() as $stemcell) {
            $i += 1;

            $stemcell->setMarketplace($marketplaceName);
            $stemcell->setStatFirstSeenAt(new \DateTime());
            $stemcell->setStatLastSeenAt($stemcell->getStatFirstSeenAt());

            $found = $repository->findOneBy([
                'marketplace' => $stemcell->getMarketplace(),
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
