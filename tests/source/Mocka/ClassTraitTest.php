<?php

namespace MockaTests\Mocka;
use Mocka;

class ClassTraitTest extends \PHPUnit_Framework_TestCase {

    public function testCallMockedMethod() {
        $mocka = new Mocka();
        $className = $mocka->mockClass('\MockaMocks\AbstractClass');
        /** @var \MockaMocks\AbstractClass $object */
        $object = new $className();
        $this->assertNull($object->foo());
        $this->assertSame('foo', $object->bar());
        

    }

}
