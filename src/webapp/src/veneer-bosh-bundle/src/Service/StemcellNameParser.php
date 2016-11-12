<?php

namespace Veneer\BoshBundle\Service;

class StemcellNameParser
{
    static public function parse($name)
    {
        // https://github.com/cppforlife/bosh-hub/blob/a745e1693e553b5a5a2b07cf76dfb82be8344a93/stemcell/stemsrepo/s3_stemcell.go#L14
        $regex = '/^bosh-(?P<inf_name>\w+)-(?P<hv_name>\w+(-\w+)?)-(?P<os_name>centos|ubuntu)(-(?P<os_version>(\w|\d)+)?(-(?P<agent_type>go_agent)))?(?P<disk_fmt>-raw)?$/';

        if (!preg_match($regex, $name, $match)) {
            throw new \InvalidArgumentException('Invalid stemcell name');
        }

        return [
            'name' => $name,
            'infrastructure' => $match['inf_name'],
            'hypervisor' => $match['hv_name'],
            'operatingSystem' => $match['os_name'],
            'operatingSystemVersion' => $match['os_version'],
            'agent' => $match['agent_type'],
        ];
    }
}
