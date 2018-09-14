<?php

use LiteFrame\Testing\TestCase;
use LiteFrame\Utility\Collection;

class CollectionTest extends TestCase
{
    protected $collection;
    protected $collectionWithKey;
    protected $sample;
    protected $sampleWithKey;
    
    public function setUp()
    {
        $this->sample = [3,49,45,100,10];
        $this->sampleWithKey = ['a'=>3,'b'=>49,'c'=>45,'d'=>100,'e'=>10];
        $this->collection = new Collection($this->sample);
        $this->collectionWithKey = new Collection($this->sampleWithKey);
    }
    
    public function testPaginate()
    {
        $subset = $this->collection->paginate(2, 1);
        $this->assertTrue(count($subset) === 2);
        $this->assertEquals($subset->toArray(), [3,49]);
        
        $subsetWithKey = $this->collectionWithKey->paginate(2, 1);
        $this->assertTrue(count($subsetWithKey) === 2);
        $this->assertEquals($subsetWithKey->toArray(), ['a'=>3,'b'=>49]);
    }
    
    public function testAll()
    {
        $this->assertEquals($this->collection->all(), $this->sample);
    }
    
    public function testToArray()
    {
        $this->assertEquals($this->collection->toArray(), $this->sample);
    }
    
    public function testGet()
    {
        $this->assertEquals($this->collection->get(2), $this->sample[2]);
        $this->assertEquals($this->collectionWithKey->get('c'), $this->sampleWithKey['c']);
    }
    
    public function testArrayAccessibility()
    {
        //Access collection directly by index
        $this->assertEquals($this->collection[2], $this->sample[2]);
        $this->assertTrue(empty($this->collectionWithKey[2]));
        //Access collection directly by key
        $this->assertTrue(empty($this->collection['c']));
        $this->assertEquals($this->collectionWithKey['c'], $this->sampleWithKey['c']);
    }
}
