<?php

use LiteFrame\Utility\Validator;

class ValidatorTest extends TestCase
{
    protected $validator;


    public function setUp()
    {
        $this->validator = new Validator;
    }
    
    /**
     * test the Validator email rule
     */
    public function testEmailValidation()
    {
        $data = array(
            'email' => 'testtest.com'
        );
        
//        $errors = $this->validator->email()->check($data);
//        $this->assertArrayHasKey('email', $errors);
    }
}
