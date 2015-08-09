<?php

namespace Bosh\CloqueBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\Expr;
use Bosh\WebBundle\Controller\AbstractController;

class DeploymentChangelogALLController extends AbstractController
{
    public function indexAction($_context)
    {
        $commits = $this->container->get('bosh_cloque.versioning.repository')->getFileLog([
            $_context['deployment']['name'] . '/bosh.yml',
            $_context['deployment']['name'] . '/infrastructure.json',
        ]);

        $ws = $this->container->get('bosh_cloque.versioning.web_service');

        foreach ($commits as $i => $commit) {
            $commits[$i]['_ws_link'] = $ws->getCommitLink($commit['commit']);
        }

        return $this->renderApi(
            'BoshCloqueBundle:DeploymentChangelogALL:index.html.twig',
            [
                'ws_link' => $ws->getRepositoryLink(),
                'commits' => $commits,
            ]
        );
    }
}
