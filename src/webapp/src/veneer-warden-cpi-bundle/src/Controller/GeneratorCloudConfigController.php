<?php

namespace Veneer\WardenCpiBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Veneer\CoreBundle\Controller\AbstractController;
use Veneer\CoreBundle\Service\Breadcrumbs;

class GeneratorCloudConfigController extends AbstractController
{
    public static function defNav(Breadcrumbs $nav)
    {
        return $nav->add(
            'cloud-config',
            [
                'veneer_wardencpi_generator_cloudconfig_summary' => [],
            ]
        );
    }

    public function summaryAction()
    {
        $yaml = $this->forward(__CLASS__.'::rawAction');

        return $this->renderApi(
            'VeneerWardenCpiBundle:GeneratorCloudConfig:summary.html.twig',
            [
                'url' => $this->container->get('router')->generate('veneer_wardencpi_generator_cloudconfig_raw'),
                'yaml' => $yaml->getContent(),
                'sha1' => explode(' ', $yaml->headers->get('content-checksum'))[1],
            ],
            [
                'def_nav' => static::defNav($this->container->get('veneer_warden_cpi.breadcrumbs')),
            ]
        );
    }

    public function rawAction()
    {
        $content = file_get_contents($this->container->get('file_locator')->locate('@VeneerWardenCpiBundle/Resources/data/cloud-config.yml'));

        return new Response(
            $content,
            200,
            [
                'content-type' => 'application/x-yaml',
                'content-disposition' => 'attachment; filename=cloud-config.yml',
                'content-checksum' => 'sha1 '.sha1($content),
            ]
        );
    }
}
