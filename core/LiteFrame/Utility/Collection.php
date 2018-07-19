<?php

namespace LiteFrame\Utility;

class Collection implements \Iterator, \ArrayAccess, \Countable
{
    protected $current = 0;
    protected $items = [];
    protected $keys = [];
    protected $map;
    protected $mapped = [];

    public function __construct(array $items)
    {
        $this->current = 0;
        $this->items = $items;
        $this->keys = array_keys($items);
    }

    public function all()
    {
        return $this->items;
    }
    
    public function keys()
    {
        return $this->keys;
    }
    
    /**
     * Apply a callable on all values and replace values with result
     * @param \LiteFrame\Utility\callable $callable
     * @param boolean $lazy set true to run mapping during access (recommended) 
     * else set false to apply immediately
     */
    public function map(callable $callable, $lazy = true) {
        if($lazy){
            $this->map = $callable;
        }else{
            //Apply immediately
            foreach ($this->items as $key => $value) {
                $this->items[$key] = $callable($value);
            }
        }
    }

    /**
     *
     * @param type $key
     * @return \Models\Project
     */
    public function get($key)
    {
        if (isset($this->items[$key])) {
            if($this->map && !in_array($key, $this->mapped)){
                $this->items[$key] = $this->map($this->items[$key]);
                $this->mapped = $key;
            }
            return $this->items[$key];
        }
        return null;
    }

    public function current()
    {
        return $this->get($this->current);
    }

    public function key()
    {
        return $this->current;
    }

    public function next()
    {
        ++$this->current;
    }

    public function rewind()
    {
        $this->current = 0;
    }

    public function valid()
    {
        return isset($this->items[$this->current]);
    }

    
    public function offsetExists($offset)
    {
        if (!isset($this->keys[$offset])) {
            return false;
        }

        $key = $this->keys[$offset];
        return isset($this->items[$key]);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->items[$this->keys[$offset]] = $value;
    }

    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->items[$this->keys[$offset]]);
            unset($this->keys[$offset]);
        }
    }

    public function count()
    {
        return count($this->items);
    }

    /**
     * Paginate collection items
     * @param int $perPage
     * @param int $page
     * @return \LiteFrame\Collection
     */
    public function paginate($perPage, $page = null)
    {
        if (empty($page)) {
            $page = request('page', 1);
        }
        $offset = ($page * $perPage) - $perPage;

        $collection = $this->all();
        $newItems = array_slice($collection, $offset, $perPage, true);
        return new self($newItems);
    }

    /**
     * Get first element in collection
     * @return mixed
     */
    public function first()
    {
        return isset($this->keys[0]) ? $this->items[$this->keys[0]] : null;
    }
    
    public function toArray()
    {
        return $this->items;
    }
    
    public function __toString()
    {
        return strval($this->items);
    }
}
