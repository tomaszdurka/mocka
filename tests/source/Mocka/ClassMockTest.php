<?php

namespace MockaTests\Mocka;

use Mocka\ClassMock;
use Mocka\ClassMockFactory;
use Mocka\ClassMockTrait;
use MockaMocks\AbstractClass;



class ClassMockTest extends \PHPUnit_Framework_TestCase {

    public function testGenerateCode() {
        $parentClassName = '\\MockaMocks\\AbstractClass';

        $classMock = new ClassMock($parentClassName, '\\Nested\\FooNamespace\\FooClass');
        $parentMockClassName = $classMock->getParentClassName();
        $expectedMockCode = <<<EOD
namespace Nested\\FooNamespace;

class FooClass extends $parentMockClassName {
}
EOD;
        $this->assertSame($expectedMockCode, $classMock->generateCode());
    }

    public function testMockMethod() {
        $factory = new ClassMockFactory();
        $classMock = $factory->loadClassMock(null, '\\MockaMocks\\AbstractClass');

        /** @var ClassMockTrait|AbstractClass $object */
        $object = $classMock->newInstanceWithoutConstructor();

        $this->assertSame('bar', $object->bar());

        $classMock->mockMethod('bar')->set(function () {
            return 'foo';
        });
        $this->assertSame('foo', $object->bar());
    }

    public function testUnmockMethod() {
        $factory = new ClassMockFactory();
        $classMock = $factory->loadClassMock(null, '\\MockaMocks\\AbstractClass');

        /** @var ClassMockTrait|AbstractClass $object */
        $object = $classMock->newInstanceWithoutConstructor();

        $this->assertSame('bar', $object->bar());

        $classMock->mockMethod('bar');
        $this->assertSame(null, $object->bar());

        $classMock->unmockMethod('bar');
        $this->assertSame('bar', $object->bar());
    }

    public function testMockMethodFromTrait() {
        $factory = new ClassMockFactory();
        $classMock = $factory->loadClassMock(null, '\\MockaMocks\\AbstractClass', null, ['\\MockaMocks\\TraitMock']);

        /** @var ClassMockTrait|AbstractClass $object */
        $object = $classMock->newInstanceWithoutConstructor();
        $this->assertSame('traitbar', $object->bar());

        $classMock->mockMethod('bar');
        $this->assertSame(null, $object->bar());
    }

    public function testMockStaticMethod() {
        $factory = new ClassMockFactory();
        $classMock = $factory->loadClassMock(null, '\\MockaMocks\\AbstractClass');
        $className = $classMock->getClassName();

        $this->assertSame('jar', $className::jar());
        $classMock->mockMethod('jar')->set(function () {
            return 'foo';
        });
        $this->assertSame('foo', $className::jar());
    }

    public function testNewInstanceConstructorArgs() {
        $factory = new ClassMockFactory();
        $classMock = $factory->loadClassMock(null, '\\MockaMocks\\AbstractClass');

        $constructorArgs = ['foo', 'bar'];
        /** @var AbstractClass $object */
        $object = $classMock->newInstance($constructorArgs);
        $this->assertSame($object->constructorArgs, $constructorArgs);
    }

    public function testNewInstanceWithoutConstructor() {
        $factory = new ClassMockFactory();
        $classMock = $factory->loadClassMock(null, '\\MockaMocks\\AbstractClass');
        $constructorRun = false;
        $classMock->mockMethod('__construct')->set(function() use (&$constructorRun) {
            $constructorRun = true;
        });
        $classMock->newInstanceWithoutConstructor();
        $this->assertFalse($constructorRun);
        $classMock->newInstance();
        $this->assertTrue($constructorRun);
    }

    public function testMockInternalClass() {
        new ClassMock(null, 'DateTime');
    }
}
