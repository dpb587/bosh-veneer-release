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
        $response = $guzzle->get($url, ['stream' => true]);

        $body = $response->getBody();
        $tmp = tempnam(sys_get_temp_dir(), 'sheaftar');
        $fh = fopen($tmp, 'w');

        while (!$body->eof()) {
            fwrite($fh, $body->read(65536));
        }

        fclose($fh);

        $tmpdir = $tmp.'-extract';
        mkdir($tmpdir, 0700, true);

        $p = new Process(
            sprintf(
                'tar -xzf %s -C %s --strip-components=1',
                escapeshellarg($tmp),
                escapeshellarg($tmpdir)
            )
        );

        $p->mustRun();

        $spec = Yaml::parse(file_get_contents($tmpdir.'/spec.yml'));

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
        return $this->storagePath.'/'.$sheaf->getId();
    }

    public function loadFullSpec(Sheaf $sheaf)
    {
        $spec = Yaml::parse(file_get_contents($this->getStoragePath($sheaf).'/spec.yml'));

        foreach ($spec['components'] as $componentIndex => $component) {
            $componentSpec = Yaml::parse(file_get_contents($this->getStoragePath($sheaf).'/'.$component['path'].'/spec.yml'));
            $componentSpec['path'] = $component['path'];
            $componentSpec['required'] = isset($componentSpec['required']) ? $componentSpec['required'] : true;

            $componentSpec['features'] = isset($componentSpec['features']) ? $componentSpec['features'] : [];

            foreach ($componentSpec['features'] as $featureIdx => $feature) {
                $feature['required'] = isset($feature['required']) ? $feature['required'] : true;
                $feature['multiple'] = isset($feature['multiple']) ? $feature['multiple'] : false;

                $componentSpec['features'][$featureIdx] = $feature;
            }

            $spec['components'][$componentIndex] = $componentSpec;
        }

        return $spec;
    }

    public function createInstallation(Sheaf $sheaf, $name, $data, RepositoryInterface $repository)
    {
        $path = 'sheaf/'.$name.'/installation.yml';
        $draftProfile = $repository->getDraftProfile('sheaf-install-'.substr(md5($path), 0, 8), $path);

        $sheafSpec = $this->loadFullSpec($sheaf);

        $writes = [
            'sheaf/'.$name.'/installation.yml' => Yaml::dump(
                array_merge(
                    $sheafSpec,
                    [
                        'installation' => $data,
                    ]
                ),
                8,
                2
            ),
            'sheaf/'.$name.'/logo.png' => file_get_contents($this->getStoragePath($sheaf).'/logo.png'),
        ];

        foreach ($sheafSpec['components'] as $component) {
            $componentEnabled = isset($component['enabled']) ? $component['enabled'] : true;

            if (!$componentEnabled) {
                continue;
            }

            if ($component['type'] == 'deployment') {
                $basedir = 'bosh/deployment/'.$name.'-'.$component['name'];

                $writes[$basedir.'/manifest.yml'] = file_get_contents($this->getStoragePath($sheaf).'/'.$component['path'].'/manifest.yml');

                foreach ($data['components'][$component['name']]['features'] as $featureName => $featureChoice) {
                    foreach ((array) $featureChoice as $choice) {
                        $src = $this->getStoragePath($sheaf).'/'.$component['path'].'/ops/'.$choice.'.yml';

                        if (file_exists($src)) {
                            $writes[$basedir.'/ops/'.$choice.'.yml'] = file_get_contents($src);
                        }

                        $src = $this->getStoragePath($sheaf).'/'.$component['path'].'/vars/'.$choice.'.yml';

                        if (file_exists($src)) {
                            $writes[$basedir.'/vars/'.$choice.'.yml'] = file_get_contents($src);
                        }
                    }
                }
            }
        }

        $repository->commitWrites($draftProfile, $writes, $name . ': prepare installation');

        return $path;
    }
}
