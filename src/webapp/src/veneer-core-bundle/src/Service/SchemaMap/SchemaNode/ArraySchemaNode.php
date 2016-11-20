<?php

namespace Veneer\CoreBundle\Service\SchemaMap\SchemaNode;

use Veneer\CoreBundle\Service\SchemaMap\DataNode\DataNodeInterface;
use Veneer\CoreBundle\Service\SchemaMap\DataNode\TraversableDataNodeInterface;
use Veneer\CoreBundle\Service\SchemaMap\SchemaNode\Factory\FactoryInterface;

class ArraySchemaNode extends AbstractSchemaNode implements EnumerableSchemaNodeInterface
{
//    /**
//     * @var DataNodeInterface[]
//     */
//    protected $children = [];
//
//    public function traverse(DataNodeInterface $data, $path)
//    {
//        $segments = explode('/', $path);
//        $segment = array_shift($segments);
//
//        if (empty($segment)) {
//            return $this;
//        }
//
//        if (isset($this->children[$segment])) {
//            return $this->children[$segment];
//        }
//
//        $node = $this->factory->traverse($data, $segment);
//
//        if (count($segments) === 0) {
//            return $node;
//        } elseif (!$data instanceof TraversableDataNodeInterface) {
//            throw new \InvalidArgumentException('Expected traversable node');
//        } elseif (!$node instanceof TraversableSchemaNodeInterface) {
//            throw new \InvalidArgumentException('Expected traversable schema');
//        }
//
//        return $node->traverse($data->traverse($segment), implode('/', $segments));
//    }

    public function enumerateProperties(DataNodeInterface $data)
    {

//        return $this->factory->getTraversablePaths($data);
    }
}
