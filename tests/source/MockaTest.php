<?php

namespace MockaTests;

class MockaTest extends \PHPUnit_Framework_TestCase {

    public function testMockClass() {
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

    public function testIntegrated() {
        $mocka = new \Mocka();
        $classMock = $mocka->mockClass('\\Mocka');

        $classMock->mockMethod('nonexistentMethod')
            ->set(function ($foo) {
                return $foo;
            })
            ->at([1, 3], function () {
                return 'bar';
            });

        $object = $classMock->newInstance();
        $this->assertSame('foo', $object->nonexistentMethod('foo'));
        $this->assertSame('bar', $object->nonexistentMethod('foo'));
        $this->assertSame('zoo', $object->nonexistentMethod('zoo'));
        $this->assertSame('bar', $object->nonexistentMethod('foo'));
    }
}
