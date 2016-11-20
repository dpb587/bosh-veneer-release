<?php

namespace Veneer\CoreBundle\Controller;

use JsonSchema\UriResolverInterface;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\CoreBundle\Service\JsonSchema\UriResolver;
use Veneer\CoreBundle\Service\Workspace\Lifecycle\LifecycleInterface;

class SchemaMapController extends AbstractController
{
    public function schemaAction(Request $request, $_format)
    {
        $storage = $this->container->get('veneer_core.schema_map.schema_storage');

        try {
            $schema = $storage->getSchema($request->query->get('uri'));
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestHttpException('Loading schema', $e);
        }

        if ($_format == 'json') {
            return new JsonResponse($schema);
        }

        $router = $this->container->get('router');
        $renderedSchema = json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $renderedSchema = htmlentities($renderedSchema);
        $renderedSchema = preg_replace_callback(
            '/&quot;(\$ref|\$schema|id)&quot;: &quot;(.+)&quot;/',
            function ($match) use ($router) {
                return sprintf(
                    '&quot;%s&quot;: &quot;<a href="%s">%s</a>&quot;',
                    $match[1],
                    $router->generate(
                        'veneer_core_schemamap_schema',
                        [
                            'uri' => $match[2]
                        ]
                    ),
                    $match[2]
                );
            },
            $renderedSchema
        );

        return new Response(
            sprintf(
                '<html><head><title>%s</title><style type="text/css">a{color:#0000CC}</style></head><body><pre><code>%s</code></pre></body></html>',
                $request->query->get('uri'),
                $renderedSchema
            )
        );
    }
}
