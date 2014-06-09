<?php

namespace MockaTests\Mocka;
use Mocka;

class ClassTraitTest extends \PHPUnit_Framework_TestCase {

    public function testCallMockedMethod() {
        $mocka = new Mocka();
        /** @var \MockaMocks\AbstractClass $object */
        $object = $mocka->mockObject('\MockaMocks\AbstractClass');
        $this->assertNull($object->foo());
        $this->assertSame('foo', $object->bar());
    }

}
