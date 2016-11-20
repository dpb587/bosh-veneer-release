<?php

namespace Veneer\CoreBundle\Service\JsonSchema;

use Doctrine\ORM\EntityManager;
use JsonSchema\Uri\Retrievers\UriRetrieverInterface;
use Doctrine\ORM\Query\Expr;

class UriRetriever implements UriRetrieverInterface
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getContentType()
    {
        return 'application/json';
    }

    public function retrieve($uri)
    {
        $parsedUri = parse_url($uri);

        if ($parsedUri['scheme'] == 'file') {
            $path = realpath($parsedUri['path']);
            $base = realpath(__DIR__ . '/../../../../');

            if (substr($path, 0, strlen($base)) != $base) {
                throw new \InvalidArgumentException('Disallowed');
            }

            return file_get_contents($parsedUri['path']);
        } elseif (($parsedUri['scheme'] == 'veneer') && ($parsedUri['host'] == 'core')) {
            return json_encode($this->handleCore($parsedUri['path']));
        } else {
            throw new \RuntimeException('unsupported');
        }
    }

    protected function handleCore($path)
    {
        if (preg_match('#/release/([^/]+)/version/([^/]+)/job/([^/]+)/(properties|links)\.json$#', $path, $match)) {
            $release = $this->em->getRepository('VeneerBoshBundle:Releases')->findOneByName($match[1]);
            if (!$release) throw new \InvalidArgumentException('Failed to find release');

            $version = $this->em->getRepository('VeneerBoshBundle:ReleaseVersions')->findOneBy(['release' => $release, 'version' => $match[2]]);
            if (!$version) throw new \InvalidArgumentException('Failed to find version');

            $job = $this->em->getRepository('VeneerBoshBundle:ReleaseVersionsTemplates')
                ->createQueryBuilder('rvt')
                ->join('rvt.template', 't')->addSelect('t')
                ->where(new Expr\Comparison('rvt.releaseVersion', '=', ':releaseVersion'))->setParameter('releaseVersion', $version)
                ->andWhere(new Expr\Comparison('t.name', '=', ':name'))->setParameter('name', $match[3])
                ->getQuery()
                ->getResult()[0]['template'];
            if (!$job) throw new \InvalidArgumentException('Failed to find release version job');

            return json_decode(json_encode($this->generateJobProperties($job, $job['propertiesJsonAsArray'])));
        }

        throw new \InvalidArgumentException($path);
    }

    protected function generateJobProperties($job, array $properties)
    {
        $schema = [
            '$schema' => 'http://json-schema.org/draft-04/schema#',
            'additionalProperties' => false,
            'properties' => [],
            'type' => 'object',
            'title' => $job['name'] . ' Configuration',
        ];

        foreach ($properties as $key => $value) {
            $ref =& $schema['properties'];

            $keySplits = explode('.', $key);
            $keySplitsMaxIdx = count($keySplits) - 1;

            foreach ($keySplits as $keySplitIdx => $keySplit) {
                if ($keySplitsMaxIdx == $keySplitIdx) {
                    $ref[$keySplit] = [];

                    $ref[$keySplit]['type'] = 'string';

                    if (isset($value['description'])) {
                        $ref[$keySplit]['description'] = $value['description'];
                    }

                    if (isset($value['default'])) {
                        $ref[$keySplit]['default'] = $value['default'];
                    }
                } else {
                    if (!isset($ref[$keySplit])) {
                        $ref[$keySplit] = [
                            'type' => 'object',
                            'properties' => [],
                        ];
                    }

                    $ref =& $ref[$keySplit]['properties'];
                }
            }
        }

        return $schema;
    }
}
