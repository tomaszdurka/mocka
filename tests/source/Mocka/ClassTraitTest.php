<?php

namespace MockaTests\Mocka;
use Mocka;

class ClassTraitTest extends \PHPUnit_Framework_TestCase {

    public function testCallMockedMethod() {
        $parentClassName = 'MockaMocks\\AbstractClass';
        $classMock = new Mocka\ClassMock($parentClassName);
        $className = $classMock->getClassName();
        $classMock->load();

        /** @var \MockaMocks\AbstractClass $object */
        $object = new $className();
        $this->assertNull($object->foo());
        $this->assertSame('foo', $object->bar());
    }

}
