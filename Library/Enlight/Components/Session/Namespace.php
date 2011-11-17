<?php
class Enlight_Components_Session_Namespace extends Zend_Session_Namespace implements Countable, IteratorAggregate, ArrayAccess
{
	public function offsetExists($key)
    {
        return $this->__isset($key);
    }
    public function offsetUnset($key)
    {
        $this->__unset($key);
    }
    public function offsetGet($key)
    {
        return $this->__get($key);
    }
    public function offsetSet($key, $value)
    {
        $this->__set($key, $value);
    }
    public function count()
    {
        return $this->apply('count');
    }
}