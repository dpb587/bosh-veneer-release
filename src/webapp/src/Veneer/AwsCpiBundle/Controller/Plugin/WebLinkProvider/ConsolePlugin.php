<?php

namespace Veneer\AwsCpiBundle\Controller\Plugin\WebLinkProvider;

use Symfony\Component\HttpFoundation\Request;
use Veneer\WebBundle\Plugin\LinkProvider\PluginInterface;
use Veneer\WebBundle\Plugin\LinkProvider\Link;

class ConsolePlugin implements PluginInterface
{
    protected $directorName;
    protected $region;

    public function __construct($directorName, $region)
    {
        $this->directorName = $directorName;
        $this->region = $region;
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
                        ->setUrl(sprintf(
                            'https://%s.console.aws.amazon.com/ec2/v2/home?region=%s#Instances:tag:director=%s;tag:deployment=%s',
                            $this->region,
                            $this->region,
                            $this->directorName,
                            $_bosh['deployment']['name']
                        )),
                ];
            case 'veneer_bosh_deployment_instance_summary':
                return [
                    (new Link('awscpi_console'))
                        ->setTopic(Link::TOPIC_CPI)
                        ->setTitle('AWS Console')
                        ->setNote('instance detail')
                        ->setUrl(sprintf(
                            'https://%s.console.aws.amazon.com/ec2/v2/home?region=%s#Instances:tag:director=%s;tag:deployment=%s;tag:Name=%s/%s',
                            $this->region,
                            $this->region,
                            $this->directorName,
                            $_bosh['deployment']['name'],
                            $_bosh['instance']['job'],
                            $_bosh['instance']['index']
                        )),
                ];
            case 'veneer_bosh_deployment_instance_persistentdisk_summary':
                return [
                    (new Link('awscpi_console'))
                        ->setTopic(Link::TOPIC_CPI)
                        ->setTitle('AWS Console')
                        ->setNote('disk detail')
                        ->setUrl(sprintf(
                            'https://%s.console.aws.amazon.com/ec2/v2/home?region=%s#Volumes:volumeId=%s',
                            $this->region,
                            $this->region,
                            $_bosh['persistent_disk']['diskCid']
                        )),
                ];
            case 'veneer_bosh_deployment_vm_summary':
                return [
                    (new Link('awscpi_console'))
                        ->setTopic(Link::TOPIC_CPI)
                        ->setTitle('AWS Console')
                        ->setNote('instance detail')
                        ->setUrl(sprintf(
                            'https://%s.console.aws.amazon.com/ec2/v2/home?region=%s#Instances:instanceId=%s',
                            $this->region,
                            $this->region,
                            $_bosh['vm']['cid']
                        )),
                ];
            case 'veneer_bosh_deployment_vm_network_summary':
                return [
                    (new Link('awscpi_console'))
                        ->setTopic(Link::TOPIC_CPI)
                        ->setTitle('AWS Console')
                        ->setNote('instance detail')
                        ->setUrl(sprintf(
                            'https://%s.console.aws.amazon.com/ec2/v2/home?region=%s#NIC:search=%s',
                            $this->region,
                            $this->region,
                            $_bosh['network']['ip']
                        )),
                ];
        }

        return [];
    }
}