<?php

namespace Veneer\MarketplaceBundle\Controller\Plugin\WebRequestContext;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\HttpFoundation\Request;
use Veneer\BoshBundle\Entity\Deployments;
use Veneer\BoshBundle\Entity\Instances;
use Veneer\BoshBundle\Entity\Vms;
use Veneer\BoshBundle\Entity\Releases;
use Veneer\WebBundle\Plugin\RequestContext\PluginInterface;
use Veneer\MarketplaceBundle\Service\MarketplaceFactory;

class BuiltinPlugin implements PluginInterface
{
    protected $factory;
    protected $em;

    public function __construct(MarketplaceFactory $factory, EntityManager $em)
    {
        $this->factory = $factory;
        $this->em = $em;
    }

    public function applyContext(Request $request, $context)
    {
        $contextSplit = explode('/', $context);
        $contextSplit[] = null;
        $contextSplit[] = null;
        $contextSplit[] = null;
        $contextSplit[] = null;

        $veneerBoshContext = [];

        if ('marketplace' == $contextSplit[0]) {
            if ('marketplace' == $contextSplit[1]) {
                try {
                    $veneerBoshContext['marketplace'] = $this->factory->get($request->attributes->get('marketplace'));
                } catch (\InvalidArgumentException $e) {
                    throw new NotFoundHttpException('Failed to find marketplace', $e);
                }

                if ('release' == $contextSplit[2]) {
                    $veneerBoshContext['release'] = [
                        'name' => $request->attributes->get('release'),
                    ];

                    if ('version' == $contextSplit[3]) {
                        $veneerBoshContext['version'] = $this->loadMarketplaceReleaseVersion(
                            $request->attributes->get('marketplace'),
                            $request->attributes->get('release'),
                            $request->attributes->get('version')
                        );
                    }
                } elseif ('stemcell' == $contextSplit[2]) {
                    $veneerBoshContext['stemcell'] = [
                        'name' => $request->attributes->get('stemcell'),
                    ];

                    if ('version' == $contextSplit[3]) {
                        $veneerBoshContext['version'] = $this->loadMarketplaceStemcellVersion(
                            $request->attributes->get('marketplace'),
                            $request->attributes->get('stemcell'),
                            $request->attributes->get('version')
                        );
                    }
                }
            }
        }

        $request->attributes->set('_bosh', $veneerBoshContext);
    }

    protected function loadMarketplaceReleaseVersion($marketplace, $release, $version)
    {
        $loaded = $this->em->getRepository('VeneerMarketplaceBundle:ReleaseVersion')->findOneBy([
            'marketplace' => $marketplace,
            'release' => $release,
            'version' => $version,
        ]);

        if (!$loaded) {
            throw new NotFoundHttpException('Failed to find release version');
        }

        return $loaded;
    }

    protected function loadMarketplaceStemcellVersion($marketplace, $stemcell, $version)
    {
        $loaded = $this->em->getRepository('VeneerMarketplaceBundle:StemcellVersion')->findOneBy([
            'marketplace' => $marketplace,
            'stemcell' => $stemcell,
            'version' => $version,
        ]);

        if (!$loaded) {
            throw new NotFoundHttpException('Failed to find stemcell version');
        }

        return $loaded;
    }
}
