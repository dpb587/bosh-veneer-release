<?php

namespace Bosh\CoreBundle\Service;

use Symfony\Component\Yaml\Yaml;

abstract class AbstractEntity implements \ArrayAccess
{
    protected $_serializationHints = [];

    public function offsetGet($offset)
    {
        if (property_exists($this, $offset)) {
            return $this->$offset;
        } elseif (preg_match('/^(.+)AsArray$/', $offset, $match)) {
            $value = $this->{$match[1]};

            if (preg_match('/Json$/', $match[1])) {
                return json_decode($value, true);
            } elseif ('{' == $value[0]) {
                return json_decode($value, true);
            } elseif ('---' == substr($value, 0, 3)) {
                return Yaml::parse($value);
            } else {
                throw new \LogicException('Failed to detect data type');
            }
        }
    }
    
    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('Read only');
    }
    
    public function offsetExists($offset)
    {
        return property_exists($this, $offset) || property_exists($this, preg_replace('/AsArray$/', '', $offset));
    }
    
    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('Read only');
    }
    
    public function setSerializationHint($property, $include)
    {
        $this->_serializationHints[$property] = $include;
        
        return $this;
    }
    
    public function toArray()
    {
        $vars = get_object_vars($this);

        foreach ($vars as $k => $v) {
            if (isset($this->_serializationHints[$k])) {
                switch ($this->_serializationHints[$k]) {
                    case true:
                        break;
                    case false:
                        unset($vars[$k]);
                }
                
                continue;
            } elseif ('_' == $k[0]) {
                unset($vars[$k]);
            
                continue;
            } elseif ($v instanceof self) {
                unset($vars[$k]);
                
                continue;
            }
        }
        
        return $vars;
    }
}
