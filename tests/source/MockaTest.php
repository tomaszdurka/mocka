<?php

namespace MockaTests;

class MockaTest extends \PHPUnit_Framework_TestCase {

    public function testMockClass(){
        $mocka = new \Mocka();
        $className = $mocka->mockClass('\\Mocka');
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
