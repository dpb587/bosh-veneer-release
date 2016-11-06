<?php

namespace Veneer\SheafBundle\Service;

use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;
use Veneer\CoreBundle\Service\Workspace\RepositoryInterface;
use Veneer\SheafBundle\Entity\Sheaf;

class ListingHelper
{
    protected $em;
    protected $storagePath;

    public function __construct(EntityManager $em, $storagePath)
    {
        $this->em = $em;
        $this->storagePath = $storagePath;
    }

    public function importTarball($url)
    {
        $guzzle = new Client();
        $response = $guzzle->get($url, [ 'stream' => true ]);

        $body = $response->getBody();
        $tmp = tempnam(sys_get_temp_dir(), 'sheaftar');
        $fh = fopen($tmp, 'w');

        while (!$body->eof()) {
            fwrite($fh, $body->read(65536));
        }

        fclose($fh);

        $tmpdir = $tmp . '-extract';
        mkdir($tmpdir, 0700, true);

        $p = new Process(
            sprintf(
                'tar -xzf %s -C %s --strip-components=1',
                escapeshellarg($tmp),
                escapeshellarg($tmpdir)
            )
        );

        $p->mustRun();

        $spec = Yaml::parse(file_get_contents($tmpdir . '/spec.yml'));

        $entity = new Sheaf();
        $entity->setSheaf($spec['name']);
        $entity->setVersion($spec['version']);

        $this->em->persist($entity);
        $this->em->flush();

        rename($tmpdir, $this->getStoragePath($entity));

        unlink($tmp);

        return $entity;
    }

    public function getStoragePath(Sheaf $sheaf)
    {
        return $this->storagePath . '/' . $sheaf->getId();
    }

    public function createInstallation(Sheaf $sheaf, $name, $data, RepositoryInterface $repository)
    {
        $path = 'sheaf/' . $name . '/installation.yml';
        $draftProfile = $repository->getDraftProfile('sheaf-install-' . substr(md5($path), 0, 8), $path);

        $writes = [
            'sheaf/' . $name . '/installation.yml' => Yaml::dump(array_merge(
                $data,
                [
                    'installation' => [
                        'name' => $sheaf->getSheaf(),
                        'version' => $sheaf->getVersion(),
                    ],
                ]
            )),
        ];

        foreach ((new Finder())->in($this->getStoragePath($sheaf))->notName('*.tgz')->files() as $path) {
            $writes['sheaf/' . $name . '/' . $path->getRelativePathname()] = file_get_contents($path);
        }

        $repository->commitWrites($draftProfile, $writes);

        return $path;
    }
}
