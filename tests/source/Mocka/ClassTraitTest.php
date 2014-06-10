<?php

namespace MockaTests\Mocka;

use Mocka;

class ClassTraitTest extends \PHPUnit_Framework_TestCase {

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
