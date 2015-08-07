<?php

namespace Bosh\WebBundle\Twig;

class Extension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'bosh_web_appendqs' => new \Twig_Filter_Method($this, 'appendQsFilter')
        );
    }

    public function appendQsFilter($url, array $qs)
    {
        return $url . ((false === strpos('?', $url)) ? '?' : '&') . http_build_query($qs);
    }

    public function getName()
    {
        return 'bosh_web';
    }
}