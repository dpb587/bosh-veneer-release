<?php

namespace Veneer\BoshBundle\Model;

class DeploymentProperties implements \ArrayAccess
{
    protected $hierarchy;

    public function __construct(array $hierarchy = [])
    {
        $this->hierarchy = $hierarchy;
    }

    public function offsetExists($offset)
    {
        try {
            $this->traversePath(explode('.', $offset));

            return true;
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }

    public function offsetGet($offset)
    {
        return $this->traversePath(explode('.', $offset));
    }

    public function offsetSet($offset, $value)
    {
        $exp = explode('.', $offset);
        $key = array_pop($exp);
        $ref = &$this->traversePath($exp, true);
        $ref[$key] = $value;
    }

    public function offsetUnset($offset)
    {
        try {
            $exp = explode('.', $offset);
            $ref = &$this->traversePath(array_slice($exp, 0, -1), true);
            unset($ref[$exp[count($exp) - 1]]);
        } catch (\InvalidArgumentException $e) {
            return;
        }
    }

    private function &traversePath(array $segments, $create = false)
    {
        $context = &$this->hierarchy;

        while (0 < count($segments)) {
            $segment = array_shift($segments);

            if ((!is_array($context)) || (!array_key_exists($segment, $context))) {
                if (!$create) {
                    throw new \InvalidArgumentException('Path does not exist');
                }

                $context[$segment] = [];
            }

            $context = &$context[$segment];
        }

        return $context;
    }
}
