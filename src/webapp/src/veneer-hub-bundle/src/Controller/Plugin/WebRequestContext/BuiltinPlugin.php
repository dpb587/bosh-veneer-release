<?php

namespace Veneer\HubBundle\Controller\Plugin\WebRequestContext;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\HttpFoundation\Request;
use Veneer\BoshBundle\Entity\Deployments;
use Veneer\BoshBundle\Entity\Instances;
use Veneer\BoshBundle\Entity\Vms;
use Veneer\BoshBundle\Entity\Releases;
use Veneer\CoreBundle\Plugin\RequestContext\PluginInterface;
use Veneer\HubBundle\Service\HubFactory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BuiltinPlugin implements PluginInterface
{
    protected $factory;
    protected $em;

    public function __construct(HubFactory $factory, EntityManager $em)
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

        if ('hub' == $contextSplit[0]) {
            if ('hub' == $contextSplit[1]) {
                try {
                    $service = $this->factory->get($request->attributes->get('hub'));

                    $veneerBoshContext['hub'] = [
                        'name' => $request->attributes->get('hub'),
                        'title' => $service->getTitle(),
                        'details' => $service->getDetails(),
                    ];
                } catch (\InvalidArgumentException $e) {
                    throw new NotFoundHttpException('Failed to find hub', $e);
                }

                if ('release' == $contextSplit[2]) {
                    $veneerBoshContext['release'] = [
                        'name' => $request->attributes->get('release'),
                    ];

                    if ('version' == $contextSplit[3]) {
                        $veneerBoshContext['version'] = $this->loadHubReleaseVersion(
                            $request->attributes->get('hub'),
                            $request->attributes->get('release'),
                            $request->attributes->get('version')
                        );
                    }
                } elseif ('stemcell' == $contextSplit[2]) {
                    $veneerBoshContext['stemcell'] = [
                        'name' => $request->attributes->get('stemcell'),
                    ];

                    if ('version' == $contextSplit[3]) {
                        $veneerBoshContext['version'] = $this->loadHubStemcellVersion(
                            $request->attributes->get('hub'),
                            $request->attributes->get('stemcell'),
                            $request->attributes->get('version')
                        );
                    }
                }
            }
        }

        $request->attributes->set('_bosh', $veneerBoshContext);
    }

    protected function loadHubReleaseVersion($hub, $release, $version)
    {
        $loaded = $this->em->getRepository('VeneerHubBundle:ReleaseVersion')->findOneBy([
            'hub' => $hub,
            'release' => $release,
            'version' => $version,
        ]);

        if (!$loaded) {
            throw new NotFoundHttpException('Failed to find release version');
        }

        return $loaded;
    }

    protected function loadHubStemcellVersion($hub, $stemcell, $version)
    {
        $loaded = $this->em->getRepository('VeneerHubBundle:StemcellVersion')->findOneBy([
            'hub' => $hub,
            'stemcell' => $stemcell,
            'version' => $version,
        ]);

        if (!$loaded) {
            throw new NotFoundHttpException('Failed to find stemcell version');
        }

        return $loaded;
    }
}
