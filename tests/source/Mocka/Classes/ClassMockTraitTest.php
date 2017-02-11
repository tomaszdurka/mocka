<?php

namespace MockaTests\Mocka\Classes;

use Mocka\Invokables\Invokable\AbstractInvokable;
use Mocka\Mocka;

class ClassMockTraitTest extends \PHPUnit_Framework_TestCase {

    public function testMockClass() {
        $mocka = new Mocka();
        $mockClass = $mocka->mockClass('\MockaMocks\AbstractClass');
        $this->assertInstanceOf('\\Mocka\\Classes\\ClassMock', $mockClass);
    }

    public function testMockInterface() {
        $mocka = new Mocka();
        $mockClass = $mocka->mockInterface('\MockaMocks\InterfaceMock');
        $this->assertInstanceOf('\\Mocka\\Classes\\ClassMock', $mockClass);
    }

    public function testCallMockedMethod() {
        $mocka = new Mocka();
        $mockClass = $mocka->mockClass('\MockaMocks\AbstractClass');
        /** @var \MockaMocks\AbstractClass|\Mocka\Classes\ClassMockTrait $object */
        $object = $mockClass->newInstanceWithoutConstructor();
        $this->assertNull($object->foo());
        $this->assertSame('jar', $object->bar());

        $mockClass->mockMethod('foo')->set(function () {
            return 'foo'; 
        });
        $this->assertSame('foo', $object->foo());

        $object->mockMethod('foo')->set(function () {
            return 'bar';
        });
        $this->assertSame('bar', $object->foo());

        $objectAnother = $mockClass->newInstanceWithoutConstructor();
        $this->assertSame('foo', $objectAnother->foo());

        $mockClass->mockMethod('foo')->set(function () {
            return 'zoo';
        });
        $this->assertSame('bar', $object->foo());
    }

    public function testClone() {
        $mocka = new Mocka();
        $mockClass = $mocka->mockClass('\MockaMocks\AbstractClass');
        $object = $mockClass->newInstanceWithoutConstructor();
        $object->getOverrides()->stub('bar');
        
        $cloned = clone $object;
        $this->assertInstanceOf(AbstractInvokable::class, $cloned->getOverrides()->get('bar'));
    }
}
