<?php

namespace Veneer\CoreBundle\Service\SchemaMap\DataNode;

class ArrayDataNode extends AbstractDataNode implements TraversableDataNodeInterface
{
    /**
     * @var DataNodeInterface[]
     */
    protected $children = [];

    public function traverse($path)
    {
        $segments = explode('/', $path);
        $segment = array_shift($segments);

        if (empty($segment)) {
            return $this;
        }

        if (isset($this->children[$segment])) {
            return $this->children[$segment];
        }

        $node = $this->buildNode($segment);

        if (count($segments) === 0) {
            return $node;
        }

        return $node->traverse(implode('/', $segments));
    }

    public function getData()
    {
        $data = parent::getData();

        foreach ($this->children as $children) {
            $data = $children->applyData($data);
        }

        return $data;
    }

    public function add(DataNodeInterface $node)
    {
        $node->setParent($this);

        $this->children[$node->getRelativePath()] = $node;

        return $this;
    }

    protected function buildNode($path)
    {
        if (strpos($path, '=') !== false) {
            if (!is_array($this->data)) {
                throw new \InvalidArgumentException(sprintf('Failed to range %s', $path));
            }

            list($pathKey, $pathValue) = explode('=', $path, 2);
            $found = 0;

            foreach ($this->data as $dataKey => $dataValue) {
                if (isset($dataValue[$pathKey]) && ($dataValue[$pathKey] == $pathValue)) {
                    $found += 1;
                    $path = $dataKey;
                }
            }

            if ($found == 0) {
                throw new \InvalidArgumentException(sprintf('Failed to find %s', $path));
            } elseif ($found > 1) {
                throw new \InvalidArgumentException(sprintf('Failed to find single %s', $path));
            }
        }

        if ($path === '-') {
            $data = [];
        } elseif (!isset($this->data[$path])) {
            throw new \InvalidArgumentException(sprintf('Failed to follow %s', $path));
        } else {
            $data = $this->data[$path];
        }

        if (is_array($data)) {
            $node = new ArrayDataNode($path);
        } else {
            $node = new ScalarDataNode($path);
        }

        $node->setData($data);
        $this->add($node);

        return $node;
    }

    public function applyData($data)
    {
        $path = $this->getRelativePath();

        if (strpos($path, '=') !== false) {
            list($pathKey, $pathValue) = explode('=', $path, 2);

            $foundKey = null;

            foreach ($this->data as $dataKey => $dataValue) {
                if (isset($dataValue[$pathKey]) && ($dataValue[$pathKey] == $pathValue)) {
                    $foundKey = $dataKey;

                    break;
                }
            }

            if ($foundKey === null) {
                throw new \RuntimeException(sprintf('Failed to find %s in %s', $this->getRelativePath(), $this->getPath()));
            }

            $path = $foundKey;
        }

        $data[$path] = $this->getData();

        return $data;
    }
}
