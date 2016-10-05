<?php

namespace MockaTests;

use Mocka\Mocka;

class MockaTest extends \PHPUnit_Framework_TestCase {

    public function testMockClass() {
        $mocka = new Mocka();
        $mock = $mocka->mockClass('\\Mocka\\Mocka');
        $className = $mock->getClassName();
        $this->assertTrue(is_subclass_of($className, '\\Mocka\\Mocka'));
        $this->assertNotSame('\\Mocka\\Mocka', $className);

        $classNameOther = $mocka->mockClass('\\Mocka\\Mocka');
        $this->assertNotSame($className, $classNameOther);
    }

    public function testMockObject() {
        $mocka = new Mocka();
        $object = $mocka->mockObject('\\Mocka\\Mocka');
        $this->assertInstanceOf('\\Mocka\\Mocka', $object);
    }

    public function testIntegrated() {
        $mocka = new Mocka();
        $classMock = $mocka->mockClass('\\MockaMocks\\AbstractClass');

        $classMock->mockMethod('bar')
            ->set(function ($foo) {
                return $foo;
            })
            ->at([1, 3], function () {
                return 'bar';
            });

        $object = $classMock->newInstanceWithoutConstructor();
        $this->assertSame('foo', $object->bar('foo'));
        $this->assertSame('bar', $object->bar('foo'));
        $this->assertSame('zoo', $object->bar('zoo'));
        $this->assertSame('bar', $object->bar('foo'));
    }

    public function testSubsequentInheritance() {
        $mocka = new Mocka();
        $mockClass = $mocka->mockClass('\\Mocka\\Mocka');
        $mockClass->mockMethod('foo')->set(function() {
            return 'foo';
        });
        $mockClassChild = $mocka->mockClass($mockClass->getClassName());
        $this->assertInstanceOf($mockClass->getClassName(), $mockClassChild->newInstanceWithoutConstructor());
        $this->assertInstanceOf('\\Mocka\\Mocka', $mockClassChild->newInstanceWithoutConstructor());
    }
}
