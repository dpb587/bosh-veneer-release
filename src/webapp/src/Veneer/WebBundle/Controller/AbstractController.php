<?php

namespace Veneer\WebBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractController extends Controller
{
    protected function validateRequest(Request $request)
    {
        return [];
    }

    public function renderApi($view, array $data = [], array $nondata = [])
    {
        $request = $this->container->get('request');
        $_format = $request->attributes->get('_format', 'html');

        if ('json' == $_format) {
            return new JsonResponse($this->normalizeApiResult($data));
        } elseif ('html' == $_format) {
            $context = $request->attributes->get('_bosh', []);

            $extras = [
                '_uuid' => md5(microtime(true)),
                '_veneer_bosh_context' => $context,
                '_links' => [],
                '_topics' => [],
            ];

            if (isset($nondata['def_nav'][-1]['route'])) {
                $extras['_links'] = $this->container->get('veneer_web.plugin.link_provider.factory')->getLinks(
                    $request,
                    $nondata['def_nav'][-1]['route'][0]
                );

                $extras['_topics'] = $this->container->get('veneer_web.plugin.topic_provider.factory')->getTopics(
                    $request,
                    $nondata['def_nav'][-1]['route'][0]
                );
            }

            return $this->render(
                $view,
                array_merge(
                    $extras,
                    $data,
                    $nondata
                )
            );
        }
    }

    protected function normalizeApiResult($result)
    {
        if (is_array($result)) {
            foreach ($result as &$v) {
                $v = $this->normalizeApiResult($v);
            }
        } elseif (is_object($result) && method_exists($result, 'toArray')) {
            return $this->normalizeApiResult($result->toArray());
        }

        return $result;
    }

    public function renderView($view, array $parameters = array())
    {
        return parent::renderView(
            $view,
            $this->getRenderParameters($parameters)
        );
    }

    public function render($view, array $parameters = array(), Response $response = null)
    {
        return $this->container->get('templating')->renderResponse(
            $view,
            $this->getRenderParameters($parameters),
            $response
        );
    }
    
    protected function getRenderParameters(array $parameters)
    {
        $request = $this->container->get('request');

        $layouts = [
            'default' => 'VeneerWebBundle:Layout:default.html.twig',
            'fragment' => 'VeneerWebBundle:Layout:fragment.html.twig',
        ];

        return array_merge(
            [
                '_layout' => $request->query->has('_frag') ? $layouts['fragment'] : $layouts['default'],
                '_frag' => $request->query->get('_frag'),
            ],
            $parameters
        );
    }
}
