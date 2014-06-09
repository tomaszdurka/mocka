<?php

namespace MockaTests\Mocka;
use Mocka;

class ClassTraitTest extends \PHPUnit_Framework_TestCase {

    public function testCallMockedMethod() {
        $mocka = new Mocka();
        $mock = $mocka->mockClass('\MockaMocks\AbstractClass');
        /** @var \MockaMocks\AbstractClass $object */
        $object = $mock->newInstance();
        $this->assertNull($object->foo());
        $this->assertSame('foo', $object->bar());
    }

}
