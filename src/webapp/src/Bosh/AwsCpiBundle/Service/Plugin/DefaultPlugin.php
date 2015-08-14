<?php

namespace Bosh\AwsCpiBundle\Service\Plugin;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query\Expr;
use Bosh\CoreBundle\Entity\Deployments;
use Bosh\CoreBundle\Entity\Vms;
use Bosh\CoreBundle\Entity\Releases;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Elastica\Client;
use Bosh\CoreBundle\Service\Plugin\PluginInterface;

class DefaultPlugin implements PluginInterface
{
    protected $directorName;
    protected $region;

    public function __construct($directorName, $region)
    {
        $this->directorName = $directorName;
        $this->region = $region;
    }

    public function getEndpoints($contextName, array $context = [])
    {
        return [];
    }

    public function getUserPrimaryLinks($contextName, array $context = [])
    {
        return [];
    }

    public function getUserReferenceLinks($contextName, array $context = [])
    {
        switch ($contextName) {
            case 'bosh/deployment':
                return [
                    'awsconsole' => [
                        'topic' => PluginInterface::USER_SECONDARY_TOPIC_CPI,
                        'title' => 'AWS Console',
                        'note' => 'all instances',
                        'url' => 'https://' . $this->region . '.console.aws.amazon.com/ec2/v2/home?region=' . $this->region . '#Instances:tag:director=' . $this->directorName . ';tag:deployment=' . $context['deployment']['name'],
                    ],
                ];
            case 'bosh/deployment/instance':
                return [
                    'awsconsole' => [
                        'topic' => PluginInterface::USER_SECONDARY_TOPIC_CPI,
                        'title' => 'AWS Console',
                        'note' => 'instance detail',
                        'url' => 'https://' . $this->region . '.console.aws.amazon.com/ec2/v2/home?region=' . $this->region . '#Instances:tag:director=' . $this->directorName . ';tag:deployment=' . $context['deployment']['name'] . ';tag:Name=' . $context['instance']['job'] . '/' . $context['instance']['index'],
                    ],
                ];
            case 'bosh/deployment/vm':
                return [
                    'awsconsole' => [
                        'topic' => PluginInterface::USER_SECONDARY_TOPIC_CPI,
                        'title' => 'AWS Console',
                        'note' => 'instance detail',
                        'url' => 'https://' . $this->region . '.console.aws.amazon.com/ec2/v2/home?region=' . $this->region . '#Instances:instanceId=' . $context['vm']['cid'],
                    ],
                ];
            case 'bosh/deployment/vm/network':
                return [
                    'awsconsole' => [
                        'topic' => PluginInterface::USER_SECONDARY_TOPIC_CPI,
                        'title' => 'AWS Console',
                        'note' => 'network interface detail',
                        'url' => 'https://' . $this->region . '.console.aws.amazon.com/ec2/v2/home?region=' . $this->region . '#NIC:search=' . $context['network']['ip'],
                    ],
                ];
            default:
                return [];
        }
    }

    public function getContext(Request $request, $contextName)
    {
        return [];
    }
}
