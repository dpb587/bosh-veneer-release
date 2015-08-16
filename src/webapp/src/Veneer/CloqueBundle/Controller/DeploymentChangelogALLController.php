<?php

namespace Veneer\CloqueBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;
use Veneer\WebBundle\Controller\AbstractController;

class DeploymentChangelogALLController extends AbstractController
{
    public function indexAction($_context)
    {
        $uncompiledPath = $this->container->getParameter('veneer_cloque.director_name') . '/' . $_context['deployment']['name'] . '/bosh.yml';

        $repo = $this->container->get('veneer_cloque.versioning.repository');

        $commits = $repo->getFileLog('compiled/' . $uncompiledPath);

        $ws = $this->container->get('veneer_cloque.versioning.web_service');

        foreach ($commits as $i => $commit) {
            $commits[$i]['_ws_link'] = $ws->getCommitLink(
                $commit['commit'],
                $repo->getFullPath($uncompiledPath)
            );
        }

        return $this->renderApi(
            'VeneerCloqueBundle:DeploymentChangelogALL:index.html.twig',
            [
                'ws_link' => $ws->getRepositoryLink(),
                'commits' => $commits,
            ]
        );
    }
}
