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
        $classMock = $mocka->mockClass('\\Mocka\\Mocka');

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

    public function testMultiInheritance() {
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
