<?php

namespace Veneer\CoreBundle\Twig;

class Extension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'veneer_core_appendqs' => new \Twig_Filter_Method($this, 'appendQsFilter'),
            'cidr_network' => new \Twig_Filter_Method($this, 'cidrNetworkFilter'),
            'cidr_netmask' => new \Twig_Filter_Method($this, 'cidrNetmaskFilter'),
            'cidr_netmask_ext' => new \Twig_Filter_Method($this, 'cidrNetmaskExtFilter'),
            'byte_format' => new \Twig_Filter_Method($this, 'byteFormatFilter'),
            'base64_encode' => new \Twig_Filter_Function('base64_encode'),
            'pluralize' => new \Twig_Filter_Method($this, 'pluralize'),
            'singularize' => new \Twig_Filter_Method($this, 'singularize'),
        );
    }

    public function cidrNetworkFilter($cidr)
    {
        list($network) = explode('/', $cidr, 2);

        return $network;
    }

    public function cidrNetmaskFilter($cidr)
    {
        list(, $mask) = explode('/', $cidr, 2);

        return $mask;
    }

    public function cidrNetmaskExtFilter($cidr)
    {
        list(, $mask) = explode('/', $cidr, 2);

        $netmask = [
            '0' => '0.0.0.0',
            '1' => '128.0.0.0',
            '2' => '192.0.0.0',
            '3' => '224.0.0.0',
            '4' => '240.0.0.0',
            '5' => '248.0.0.0',
            '6' => '252.0.0.0',
            '7' => '254.0.0.0',
            '8' => '255.0.0.0',

            '9' => '255.128.0.0',
            '10' => '255.192.0.0',
            '11' => '255.224.0.0',
            '12' => '255.240.0.0',
            '13' => '255.248.0.0',
            '14' => '255.252.0.0',
            '15' => '255.254.0.0',
            '16' => '255.255.0.0',

            '17' => '255.255.128.0',
            '18' => '255.255.192.0',
            '19' => '255.255.224.0',
            '20' => '255.255.240.0',
            '21' => '255.255.248.0',
            '22' => '255.255.252.0',
            '23' => '255.255.254.0',
            '24' => '255.255.255.0',

            '25' => '255.255.255.128',
            '26' => '255.255.255.192',
            '27' => '255.255.255.224',
            '28' => '255.255.255.240',
            '29' => '255.255.255.248',
            '30' => '255.255.255.252',
            '31' => '255.255.255.254',
            '32' => '255.255.255.255',
        ];

        return $netmask[$mask];
    }

    // https://github.com/BrazilianFriendsOfSymfony/BFOSTwigExtensionsBundle/blob/39e9089f17f44f93656cc29fbfed4ba9e4d3fef1/Twig/MiscExtension.php#L50
    public function byteFormatFilter($bytes, $round = 1)
    {
        $si = true;
        $unit = $si ? 1000 : 1024;
        if ($bytes <= $unit) {
            return $bytes.' B';
        }

        $exp = intval((log($bytes) / log($unit)));
        $pre = ($si ? 'kMGTPE' : 'KMGTPE');
        $pre = $pre[$exp - 1].($si ? '' : 'i');

        return sprintf('%.'.$round.'f %sB', $bytes / pow($unit, $exp), $pre);
    }

    public function appendQsFilter($url, array $qs)
    {
        return $url.((false === strpos($url, '?')) ? '?' : '&').http_build_query($qs);
    }

    public function pluralize($text)
    {
        return \Doctrine\Common\Inflector\Inflector::pluralize($text);
    }

    public function singularize($text)
    {
        return \Doctrine\Common\Inflector\Inflector::singularize($text);
    }

    public function getName()
    {
        return 'veneer_web';
    }
}
