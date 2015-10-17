<?php

namespace Veneer\MarketplaceBundle\Service;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManager;

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

        foreach ($marketplaceService->yieldReleases() as $release) {
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

                $found->setStatLastSeenAt($release->getStatLastSeenAt());

                $release = $found;
            } else {
                $logger->info(sprintf('release %s/%s (new)', $release->getRelease(), $release->getVersion()));
            }

            $this->em->persist($release);
            $this->em->flush();
        }

        $this->em->commit();
    }

    public function updateStemcells($marketplaceName, LoggerInterface $logger)
    {
        $marketplaceService = $this->factory->get($marketplaceName);
        $repository = $this->em->getRepository('VeneerMarketplaceBundle:StemcellVersion');

        $this->em->beginTransaction();

        foreach ($marketplaceService->yieldStemcells() as $stemcell) {
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

                $found->setStatLastSeenAt($stemcell->getStatLastSeenAt());

                $stemcell = $found;
            } else {
                $logger->info(sprintf('stemcell %s/%s (new)', $stemcell->getStemcell(), $stemcell->getVersion()));
            }

            $this->em->persist($stemcell);
            $this->em->flush();
        }

        $this->em->commit();
    }
}
