<?php

namespace Veneer\MarketplaceBundle\Service\Marketplace;

use Veneer\MarketplaceBundle\Entity\ReleaseVersion;
use Veneer\MarketplaceBundle\Entity\StemcellVersion;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use Aws\S3\S3Client;

class AwsS3Marketplace implements MarketplaceInterface
{
    protected $title;
    protected $options;

    private $client;

    public function __construct($title = null, array $options = [])
    {
        $this->title = (null === $title) ? 'aws-s3' : $title;
        $this->options = array_merge(
            [
                'region' => 'us-east-1',
                'bucket' => null,
                'access_key_id' => null,
                'secret_access_key' => null,
                'release_prefix' => 'release/',
                'release_regex' => '#/(?P<name>[^/]+)\-(?P<version>\d.*)\.tgz$#',
                'stemcell_prefix' => 'stemcell/',
                'stemcell_regex' => '#/(?P<name>[^/]+)\-(?P<version>\d.*)(?<light>\-light)?\.tgz$#',
            ],
            $options
        );
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function yieldReleases()
    {
        $regex = $this->options['release_regex'];

        if (null == $regex) {
            return;
        }

        $client = $this->getClient();
        $match = null;

        foreach ($this->getObjectIterator($this->options['release_prefix']) as $object) {
            if (!preg_match($regex, $object['Key'], $match)) {
                continue;
            }

            list($name, $version, $extra) = $this->extractNameVersion($match);

            $entity = new ReleaseVersion();
            $entity->setRelease($name);
            $entity->setVersion($version);
            $entity->setTarballUrl($client->createPresignedRequest(
                $client->getCommand(
                    'GetObject',
                    [
                        'Bucket' => $this->options['bucket'],
                        'Key' => $object['Key'],
                    ]
                ),
                0
            )->getUri());
            $entity->setTarballSize($object['Size']);
            $entity->setTarballChecksum('md5:' . $object['ETag']);

            yield $entity;
        }
    }

    public function authenticateReleaseTarballUrl($tarballUrl)
    {
        // @todo
    }

    public function yieldStemcells()
    {
        $regex = $this->options['stemcell_regex'];

        if (null == $regex) {
            return;
        }

        $client = $this->getClient();
        $match = null;

        foreach ($this->getObjectIterator($this->options['stemcell_prefix']) as $object) {
            if (!preg_match($regex, $object['Key'], $match)) {
                continue;
            }

            list($name, $version, $extra) = $this->extractNameVersion($match);

            $entity = new StemcellVersion();
            $entity->setStemcell($name);
            $entity->setVersion($version);
            $entity->setSourceType(isset($extra['light']) ? 'light' : 'regular');
            $entity->setTarballUrl($client->createPresignedRequest(
                $client->getCommand(
                    'GetObject',
                    [
                        'Bucket' => $this->options['bucket'],
                        'Key' => $object['Key'],
                    ]
                ),
                0
            )->getUri());
            $entity->setTarballSize($object['Size']);
            $entity->setTarballChecksum('md5:' . $object['ETag']);

            yield $entity;
        }
    }

    public function authenticateStemcellTarballUrl($tarballUrl)
    {
        // @todo
    }

    protected function getClient()
    {
        if (null === $this->client) {
            $this->client = new S3Client([
                'version' => '2006-03-01',
                'region' => $this->options['region'],
                'credentials' => [
                    'key'    => $this->options['access_key_id'],
                    'secret'    => $this->options['secret_access_key'],
                ],
            ]);
        }

        return $this->client;
    }

    protected function getObjectIterator($prefix)
    {
        return $this->getClient()->getIterator(
            'ListObjects',
            [
                'Bucket' => $this->options['bucket'],
                'Prefix' => $prefix,
            ]
        );
    }

    protected function extractNameVersion(array $pairs)
    {
        $extra = [];

        ksort($pairs);

        foreach ($pairs as $k => $v) {
            if (!preg_match('/^(\w+)(\d+|)$/', $k, $match)) {
                continue;
            }

            $extra[$match[1]][] = $v;
        }

        return [
            implode('', $extra['name']),
            implode('', $extra['version']),
            $extra,
        ];
    }
}
