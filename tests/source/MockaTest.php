<?php

namespace MockaTests;

class MockaTest extends \PHPUnit_Framework_TestCase {

    public function testMockClass(){
        $mocka = new \Mocka();
        $mock = $mocka->mockClass('\\Mocka');
        $className = $mock->getClassName();
        $this->assertTrue(is_subclass_of($className, '\\Mocka'));
        $this->assertNotSame('\\Mocka', $className);
        $this->assertStringStartsWith('\\Mocka', $className);

        $classNameOther = $mocka->mockClass('\\Mocka');
        $this->assertNotSame($className, $classNameOther);
    }

    public function testMockObject() {
        $mocka = new \Mocka();
        $object = $mocka->mockObject('\\Mocka');
        $this->assertInstanceOf('\\Mocka', $object);
    }
}
