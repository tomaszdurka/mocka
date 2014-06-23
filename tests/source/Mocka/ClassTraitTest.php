<?php

namespace MockaTests\Mocka;

use \Mocka\Mocka;

class ClassTraitTest extends \PHPUnit_Framework_TestCase {

    public function testMockClass() {
        $mocka = new Mocka();
        $mockClass = $mocka->mockClass('\MockaMocks\AbstractClass');
        $this->assertInstanceOf('\\Mocka\\ClassMock', $mockClass);
    }

    public function testMockInterface() {
        $mocka = new Mocka();
        $mockClass = $mocka->mockInterface('\MockaMocks\InterfaceMock');
        $this->assertInstanceOf('\\Mocka\\ClassMock', $mockClass);
    }

    public function testCallMockedMethod() {
        $mocka = new Mocka();
        $mockClass = $mocka->mockClass('\MockaMocks\AbstractClass');
        /** @var \MockaMocks\AbstractClass|\Mocka\ClassTrait $object */
        $object = $mockClass->newInstance();
        $this->assertNull($object->foo());
        $this->assertSame('bar', $object->bar());

        $object->mockMethod('foo')->set(function () {
            return 'foo';
        });
        $this->assertSame('foo', $object->foo());
    }
}
