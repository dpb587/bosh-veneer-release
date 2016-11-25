<?php

namespace Veneer\CoreBundle\Service\Storage\Layer;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;
use Veneer\CoreBundle\Service\Storage\Object\Directory;
use Veneer\CoreBundle\Service\Storage\Object\File;
use Veneer\CoreBundle\Service\Storage\Object\YamlFile;
use Veneer\CoreBundle\Service\Storage\Query\DirectoryQuery;
use Veneer\CoreBundle\Service\Storage\Query\FileQuery;

class BoshReadableLayer implements LayerInterface
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    protected function createYamlFile($path, $data)
    {
        // symfony yaml does not parse the same way as ruby :(
        $p = new Process('ruby -e "require \'yaml\';require \'json\';puts JSON.generate(YAML.load(\$stdin.read))"', null, [], $data);
        $p->mustRun();

        return (new YamlFile($path))->setData(Yaml::dump(json_decode($p->getOutput(), true), 8));
    }

    public function get(FileQuery $query)
    {
        $path = $query->getPath();

        if ($path == 'bosh/cloud-config/manifest.yml') {
            $found = $this->em->getRepository('VeneerBoshBundle:CloudConfigs')->findOneBy([], ['id' => 'DESC']);
            if (!$found) return;

            $query->setFile($this->createYamlFile($path, $found['properties']));
        } elseif ($path == 'bosh/runtime-config/manifest.yml') {
            $found = $this->em->getRepository('VeneerBoshBundle:RuntimeConfigs')->findOneBy([], ['id' => 'DESC']);
            if (!$found) return;

            $query->setFile($this->createYamlFile($path, $found['properties']));
        } elseif (preg_match('#^bosh/deployment/([^/]+)/manifest.yml$#', $path, $match)) {
            $found = $this->em->getRepository('VeneerBoshBundle:Deployments')->findOneBy(['name' => $match[1]]);
            if (!$found) return;

            $query->setFile($this->createYamlFile($path, $found['manifest']));
        } else {
            return;
        }

        $query->successful();
    }

    public function ls(DirectoryQuery $query)
    {
        $path = $query->getPath();

        if ($path == '') {
            $query->addChild(new Directory('bosh'));
        } elseif ($path == 'bosh') {
            $query->addChild(new Directory('bosh/cloud-config'));
            $query->addChild(new Directory('bosh/deployment'));
            $query->addChild(new Directory('bosh/runtime-config'));
        } elseif ($path == 'bosh/cloud-config') {
            $query->addChild(new File('bosh/cloud-config/manifest.yml'));
        } elseif ($path == 'bosh/runtime-config') {
            $query->addChild(new File('bosh/runtime-config/manifest.yml'));
        } elseif ($path == 'bosh/deployment') {
            foreach ($this->em->getRepository('VeneerBoshBundle:Deployments')->findBy([], ['name' => 'ASC']) as $subject) {
                $query->addChild(new Directory('bosh/deployment/' . $subject['name']));
            }
        } elseif (preg_match('#^bosh/deployment/([^/]+)$#', $path, $match)) {
            $query->addChild(new File($path . '/manifest.yml'));
        } else {
            return;
        }

        $query->successful();
    }

    public function rm(FileQuery $query)
    {
        // not supported
    }

    public function put(FileQuery $query)
    {
        // not supported
    }
}
