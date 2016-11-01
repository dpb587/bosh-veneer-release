<?php

namespace Veneer\AwsCpiBundle\Controller\Plugin\WebLinkProvider;

use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Plugin\LinkProvider\PluginInterface;
use Veneer\CoreBundle\Plugin\LinkProvider\Link;
use Veneer\AwsCpiBundle\Service\ConsoleHelper;

class ConsolePlugin implements PluginInterface
{
    protected $consoleHelper;
    protected $directorName;

    public function __construct(ConsoleHelper $consoleHelper, $directorName)
    {
        $this->consoleHelper = $consoleHelper;
        $this->directorName = $directorName;
    }

    public function getLinks(Request $request, $route)
    {
        $_bosh = $request->attributes->get('_bosh');

        switch ($route) {
            case 'veneer_bosh_deployment_summary':
                return [
                    (new Link('awscpi_console'))
                        ->setTopic(Link::TOPIC_CPI)
                        ->setTitle('AWS Console')
                        ->setNote('all instances')
                        ->setUrl($this->consoleHelper->getEc2InstanceSearch([
                            'tag:director' => $this->directorName,
                            'tag:deployment' => $_bosh['deployment']['name'],
                        ])),
                ];
            case 'veneer_bosh_deployment_instancegroup_instance_summary':
                return [
                    (new Link('awscpi_console'))
                        ->setTopic(Link::TOPIC_CPI)
                        ->setTitle('AWS Console')
                        ->setNote('instance detail')
                        ->setUrl($this->consoleHelper->getEc2InstanceSearch($_bosh['instance']['vmCid'])),
                ];
            case 'veneer_bosh_deployment_instancegroup_instance_persistentdisk_summary':
                return [
                    (new Link('awscpi_console'))
                        ->setTopic(Link::TOPIC_CPI)
                        ->setTitle('AWS Console')
                        ->setNote('disk detail')
                        ->setUrl($this->consoleHelper->getEc2VolumeSearch($_bosh['persistent_disk']['diskCid'])),
                ];
            case 'veneer_bosh_deployment_instancegroup_instance_network_summary':
                return [
                    isset($_bosh['network']['type']) && ('eip' == $_bosh['network']['type'])
                        ? (new Link('awscpi_console'))
                            ->setTopic(Link::TOPIC_CPI)
                            ->setTitle('AWS Console')
                            ->setNote('allocation detail')
                            ->setUrl($this->consoleHelper->getEc2EipAllocationSearch($_bosh['network']['ip']))
                        : (new Link('awscpi_console'))
                            ->setTopic(Link::TOPIC_CPI)
                            ->setTitle('AWS Console')
                            ->setNote('interface detail')
                            ->setUrl($this->consoleHelper->getEc2NicSearch([ 'search' => $_bosh['network']['ip'] ]))
                    ,
                ];
        }

        return [];
    }
}