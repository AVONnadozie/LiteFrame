<?php

use LiteFrame\Database\DB;
use LiteFrame\Testing\TestCase;

class ModelTest extends TestCase {

    protected $model;
    public static $fired = [];

    public function setUp() {
        include_once __DIR__ . '/SampleModel.php';

        $this->model = SampleModel::dispense();
        $this->model->beforeSave(function() {
            ModelTest::$fired[] = 'beforeSave';
            return true;
        });
        $this->model->beforeCreate(function() {
            ModelTest::$fired[] = 'beforeCreate';
            return true;
        });
        $this->model->beforeUpdate(function() {
            ModelTest::$fired[] = 'beforeUpdate';
            return true;
        });
        $this->model->beforeTrash(function() {
            ModelTest::$fired[] = 'beforeTrash';
            return true;
        });

        $this->model->afterSave(function() {
            ModelTest::$fired[] = 'afterSave';
            return true;
        });
        $this->model->afterCreate(function() {
            ModelTest::$fired[] = 'afterCreate';
            return true;
        });
        $this->model->afterUpdate(function() {
            ModelTest::$fired[] = 'afterUpdate';
            return true;
        });
        $this->model->afterTrash(function() {
            ModelTest::$fired[] = 'afterTrash';
            return true;
        });
    }

    public function testEvents() {
        //Create new
        $this->model->name = "Testing";
        $this->model->save();
        //Check for save and create events
        $this->assertContains('beforeSave', ModelTest::$fired);
        $this->assertContains('beforeCreate', ModelTest::$fired);
        $this->assertContains('afterSave', ModelTest::$fired);
        $this->assertContains('afterCreate', ModelTest::$fired);
        //Update
        $this->model->name = "Testing 2";
        $this->model->save();
        //Check for save and update
        $this->assertContains('beforeSave', ModelTest::$fired);
        $this->assertContains('beforeUpdate', ModelTest::$fired);
        $this->assertContains('afterSave', ModelTest::$fired);
        $this->assertContains('afterUpdate', ModelTest::$fired);
        //Delete
        $this->model->trash();
        $this->assertContains('beforeTrash', ModelTest::$fired);
        $this->assertContains('afterTrash', ModelTest::$fired);
        DB::wipe($this->model->getTable());
    }

}
