<?php

namespace Veneer\CoreBundle\Service\JsonSchema;

use JsonSchema\Uri\UriResolver as BaseUriResolver;

class UriResolver extends BaseUriResolver
{
    protected function getTranslations()
    {
        return [
            #'https://dpb587.github.io/bosh-json-schema/default/cpi/' => 'file://' . realpath(__DIR__ . '/../../../../veneer-bosh-bundle/src/Resources/schema-map/dev/cpi') . '/',
            #'https://dpb587.github.io/bosh-json-schema/default/cpi/' => 'file://' . realpath(__DIR__ . '/../../../../veneer-warden-cpi-bundle/src/Resources/schema-map/dev') . '/',
            'https://dpb587.github.io/bosh-json-schema/default/cpi/' => 'file://' . realpath(__DIR__ . '/../../../../veneer-aws-cpi-bundle/src/Resources/schema-map/dev') . '/',
            'https://dpb587.github.io/bosh-json-schema/default/director/' => 'file://' . realpath(__DIR__ . '/../../../../veneer-bosh-bundle/src/Resources/schema-map/dev/director') . '/',
            'https://dpb587.github.io/bosh-json-schema/default/aws-cpi/' => 'file://' . realpath(__DIR__ . '/../../../../veneer-aws-cpi-bundle/src/Resources/schema-map/dev') . '/',
            'https://dpb587.github.io/bosh-json-schema/default/warden-cpi/' => 'file://' . realpath(__DIR__ . '/../../../../veneer-warden-cpi-bundle/src/Resources/schema-map/dev') . '/',
        ];
    }

    public function resolve($uri, $baseUri = null)
    {
        # let the parent do the relative->absolute path
        $resolved = parent::resolve($uri, $baseUri);
        $resolvedScheme = parse_url($resolved, PHP_URL_SCHEME);

        if ('file' == $resolvedScheme) {
            return $resolved;
        }

        foreach ($this->getTranslations() as $from => $to) {
            if (preg_match('@^(' . preg_quote($from, '@') . ')(.*)$@', $resolved, $match)) {
                return $to . $match[2];
            }
        }

        return $resolved;
    }

    public function reverseResolve($uri)
    {
        foreach (array_flip($this->getTranslations()) as $from => $to) {
            if (preg_match('@^(' . preg_quote($from, '@') . ')(.*)$@', $uri, $match)) {
                return $to . $match[2];
            }
        }

        return $uri;
    }
}
