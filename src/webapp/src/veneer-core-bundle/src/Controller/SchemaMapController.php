<?php

namespace Veneer\CoreBundle\Controller;

use JsonSchema\UriResolverInterface;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Veneer\CoreBundle\Service\Breadcrumbs;
use Veneer\CoreBundle\Service\JsonSchema\UriResolver;
use Veneer\CoreBundle\Service\Workspace\Lifecycle\LifecycleInterface;

class SchemaMapController extends AbstractController
{
    public function schemaAction(Request $request, $_format)
    {
        $resolver = $this->container->get('veneer_core.schema_map.schema_storage.url_resolver');
        $resolvedUri = $resolver->resolve($request->query->get('uri'));

        $storage = $this->container->get('veneer_core.schema_map.schema_storage');
        $schema = $storage->getSchema($resolvedUri);

        $presentableSchema = $this->convertSchema($resolver, $schema);

        if ($_format == 'json') {
            return new JsonResponse($presentableSchema);
        }

        $router = $this->container->get('router');
        $parsedSchema = json_encode($presentableSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $parsedSchema = htmlentities($parsedSchema);
        $parsedSchema = preg_replace_callback(
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
            $parsedSchema
        );

        return new Response(
            sprintf(
                '<html><head><title>%s</title></head><body><pre><code>%s</code></pre></body></html>',
                $request->query->get('uri'),
                $parsedSchema
            )
        );
    }

    protected function convertSchema(UriResolver $resolver, \stdClass $schema)
    {
        if (isset($schema->id)) {
            $schema->id = $resolver->reverseResolve($schema->id);
        }

        if (isset($schema->{'$ref'})) {
            $schema->{'$ref'} = $resolver->reverseResolve($schema->{'$ref'});
        }

        foreach ($schema as $key => $value) {
            if ($value instanceof \stdClass) {
                $schema->$key = $this->convertSchema($resolver, $value);
            }
        }

        return $schema;
    }
}
