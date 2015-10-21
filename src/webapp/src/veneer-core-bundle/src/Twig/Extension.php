<?php

namespace Veneer\CoreBundle\Twig;

class Extension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'veneer_core_appendqs' => new \Twig_Filter_Method($this, 'appendQsFilter'),
            'byte_format' => new \Twig_Filter_Method($this, 'byteFormatFilter'),
        );
    }

    // https://github.com/BrazilianFriendsOfSymfony/BFOSTwigExtensionsBundle/blob/39e9089f17f44f93656cc29fbfed4ba9e4d3fef1/Twig/MiscExtension.php#L50
    public function byteFormatFilter($bytes, $round = 1)
    {
        $si = true;
        $unit = $si ? 1000 : 1024;
        if ($bytes <= $unit) {
            return $bytes . ' B';
        }

        $exp = intval((log($bytes) / log($unit)));
        $pre = ($si ? "kMGTPE" : "KMGTPE");
        $pre = $pre[$exp - 1] . ($si ? '' : 'i');

        return sprintf('%.' . $round . 'f %sB', $bytes / pow($unit, $exp), $pre);
    }

    public function appendQsFilter($url, array $qs)
    {
        return $url . ((false === strpos('?', $url)) ? '?' : '&') . http_build_query($qs);
    }

    public function getName()
    {
        return 'veneer_web';
    }
}