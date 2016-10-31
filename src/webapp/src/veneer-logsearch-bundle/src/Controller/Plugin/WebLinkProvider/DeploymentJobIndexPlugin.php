<?php

namespace Veneer\LogsearchBundle\Controller\Plugin\WebLinkProvider;

use Symfony\Component\HttpFoundation\Request;
use Veneer\CoreBundle\Plugin\LinkProvider\PluginInterface;
use Veneer\CoreBundle\Plugin\LinkProvider\Link;

class DeploymentInstanceGroupIndexPlugin implements PluginInterface
{
    protected $kibanaUrl;
    protected $directorName;

    public function __construct($kibanaUrl, $directorName)
    {
        $this->kibanaUrl = $kibanaUrl;
        $this->directorName = $directorName;
    }

    public function getLinks(Request $request, $route)
    {
        $_bosh = $request->attributes->get('_bosh');

        return [
            (new Link('cloque_kibana_jobmetrics'))
                ->setTopic(Link::TOPIC_PERFORMANCE)
                ->setTitle('Host Stats')
                ->setNote('Kibana')
                ->setUrl(sprintf(
                    '%s#/dashboard/elasticsearch/job-metrics?director=%s&deployment=%s&job=%s',
                    $this->kibanaUrl,
                    rawurlencode($this->directorName),
                    rawurlencode($_bosh['deployment']['name']),
                    rawurlencode($_bosh['job']['job'] . '/' . $_bosh['index']['index'])
                )),
        ];
    }
}
