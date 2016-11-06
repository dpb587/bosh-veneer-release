<?php

namespace Veneer\AwsCpiBundle\Service;

class ConsoleHelper
{
    protected $region;

    public function __construct($region)
    {
        $this->region = $region;
    }

    public function getEc2InstanceSearch($search)
    {
        return $this->createSearchLinkV2(
            'ec2',
            'Instances',
            is_array($search) ? $search : ['instanceId' => $search]
        );
    }

    public function getEc2VolumeSearch($search)
    {
        return $this->createSearchLinkV2(
            'ec2',
            'Volumes',
            is_array($search) ? $search : ['volumeId' => $search]
        );
    }

    public function getEc2NicSearch($search)
    {
        return $this->createSearchLinkV2(
            'ec2',
            'NIC',
            is_array($search) ? $search : ['interfaceId' => $search]
        );
    }

    public function getEc2EipAllocationSearch($search)
    {
        return $this->createSearchLinkV2(
            'ec2',
            'Addresses',
            is_array($search) ? $search : ['publicIp' => $search]
        );
    }

    public function getEc2SecurityGroupSearch($search)
    {
        return $this->createSearchLinkV2(
            'ec2',
            'SecurityGroups',
            is_array($search) ? $search : ['groupId' => $search]
        );
    }

    public function getVpcSubnetSearch($search)
    {
        return $this->createSearchLinkV1(
            'vpc',
            'subnets',
            is_array($search) ? $search : ['subnetId' => $search]
        );
    }

    protected function createSearchLinkV1($product, $context, array $search)
    {
        return sprintf(
            'https://%s.console.aws.amazon.com/%s/home?region=%s#%s',
            $this->region,
            $product,
            $this->region,
            $context.':filter='.implode(';', $search)
        );
    }

    protected function createSearchLinkV2($product, $context, array $search)
    {
        $fragment = [];

        foreach ($search as $searchIdx => $searchTerm) {
            $fragment[] = $searchIdx.'='.$searchTerm;
        }

        return sprintf(
            'https://%s.console.aws.amazon.com/%s/v2/home?region=%s#%s',
            $this->region,
            $product,
            $this->region,
            $context.':'.implode(';', $fragment)
        );
    }
}
