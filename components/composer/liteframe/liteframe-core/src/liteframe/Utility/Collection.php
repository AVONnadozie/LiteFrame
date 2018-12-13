<?php

namespace LiteFrame\Utility;

class Collection implements \Iterator, \ArrayAccess, \Countable
{
    protected $items = [];
    protected $maps = [];
    protected $mapped = [];

    public function __construct($items)
    {
        $this->setItems($items);
    }

    public function setItems($items)
    {
        if ($items instanceof self) {
            $this->copy($items, true);
        } else {
            $this->items = $items;
        }
    }

    private function createChildCollection(array $items)
    {
        $collection = new self($items);
        $collection->copy($this);
        return $collection;
    }

    private function copy($original, $withItems = false)
    {
        if ($withItems) {
            $this->items = $original->items;
        }
        $this->maps = $original->maps;
        $this->mapped = $original->mapped;
    }

    public function all()
    {
        return $this->items;
    }
    
    public function keys()
    {
        return array_keys($this->items);
    }
    
    /**
     * Apply a callable on all values and replace values with result
     * @param \LiteFrame\Utility\callable $callable
     * @param boolean $lazy set true to run mapping during access (recommended)
     * else set false to apply immediately
     */
    public function map(callable $callable)
    {
        $this->maps[] = $callable;
    }

    /**
     *
     * @param type $key
     */
    public function get($key)
    {
        if (isset($this->items[$key])) {
            $item = $this->items[$key];
            if (is_array($item)) {
                return $this->createChildCollection($item);
            }

            if (!empty($this->maps) && !in_array($key, $this->mapped)) {
                foreach ($this->maps as $map) {
                    $this->items[$key] = $map($this->items[$key]);
                    $this->mapped[] = $key;
                }
            }
            return $this->items[$key];
        }
        return null;
    }

    public function current()
    {
        return $this->get($this->key());
    }

    public function key()
    {
        return key($this->items);
    }

    public function next()
    {
        return next($this->items);
    }

    public function rewind()
    {
        reset($this->items);
    }

    public function valid()
    {
        $key = $this->key();
        return ($key !== null && $key !== false);
    }

    
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->items[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
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
        return $this->createChildCollection($newItems);
    }

    /**
     * Get first element in collection
     * @return mixed
     */
    public function first()
    {
        foreach ($this->items as $value) {
            return $value;
        }
    }

    public function flatten()
    {
        $this->items = static::flatten_list($this);
        return $this;
    }

    private static function flatten_list($array, $result = array())
    {
        if ($array instanceof self) {
            $array = $array->toArray();
        }

        foreach ($array as $value) {
            if (is_array($value)) {
                $result = static::flatten_list($value, $result);
            } else {
                $result[] = $value;
            }
        }
        return $result;
    }

    public function chunk($size, $preservKeys = false)
    {
        $chunk = array_chunk($this->items, $size, $preservKeys);
        return $this->createChildCollection($chunk);
    }

    public function toArray()
    {
        return $this->items;
    }
    
    public function __toString()
    {
        return json_encode($this->toArray());
    }
}
