<?php

use LiteFrame\Testing\TestCase;
use LiteFrame\Utility\Inflector;

class InflectorTest extends TestCase {

    private $testSentence;
    private $testWord;

    public function setUp() {
        $this->testSentence = 'FaVorite 2018/2019 food_able';
        $this->testWord = 'food';
    }

    public function testSlugify() {
        $slug = Inflector::slugify($this->testSentence);
        $this->assertNotRegExp('#[/\s]#', $slug);
    }

    public function testUnderscore() {
        $expected = 'Fa_Vorite_2018/2019_food_able';
        $slug = Inflector::underscore($this->testSentence);
        $this->assertEquals($expected, $slug);
    }

    public function testRedBeantable() {
        $expected = 'favorite20182019foodables';
        $slug = Inflector::redbeantable($this->testSentence);
        $this->assertEquals($expected, $slug);
    }

    public function testCamelize() {
        $expected = 'faVorite2018/2019FoodAble';
        $slug = Inflector::camelize($this->testSentence);
        $this->assertEquals($expected, $slug);
    }

    public function testClassify() {
        $expected = 'FaVorite2018/2019FoodAble';
        $slug = Inflector::classify($this->testSentence);
        $this->assertEquals($expected, $slug);
    }

    public function testWordRule() {
        $pslug = Inflector::pluralize($this->testWord);
        $this->assertEquals('foods', $pslug);

        $slug = Inflector::singularize($pslug);
        $this->assertEquals($this->testWord, $slug);
    }

}
