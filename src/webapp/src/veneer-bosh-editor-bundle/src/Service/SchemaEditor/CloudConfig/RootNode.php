<?php

namespace Veneer\BoshEditorBundle\Service\SchemaEditor\CloudConfig;

class RootNode extends AbstractNode
{
    protected $data;
    protected $nodes;

    public function __construct(array $data)
    {
        $this->data = $data;

        $this->registerNode('availability_zones', function () { return IndexedArrayNode('name', AvailabilityZoneNode::class); });
        $this->registerNode('disk_types', function () { return IndexedArrayNode('name', DiskTypeNode::class); });
    }
}