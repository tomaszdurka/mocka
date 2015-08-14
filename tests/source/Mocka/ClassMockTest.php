<?php

namespace MockaTests\Mocka;

use Mocka\ClassMock;
use Mocka\AbstractClassTrait;
use MockaMocks\AbstractClass;

class ClassMockTest extends \PHPUnit_Framework_TestCase {

    public function testGenerateCode() {
        $parentClassName = '\\MockaMocks\\AbstractClass';

        $classMock = new ClassMock('\\Nested\\FooNamespace\\FooClass', $parentClassName);
        $parentMockClassName = $classMock->getParentClassName();
        $expectedMockCode = <<<EOD
namespace Nested\\FooNamespace;

class FooClass extends \\$parentMockClassName {

    use \\Mocka\\ClassTrait;
}
EOD;
        $this->assertSame($expectedMockCode, $classMock->generateCode());
    }

    public function testMockMethod() {
        $parentClassName = '\\MockaMocks\\AbstractClass';
        $classMock = new ClassMock(null, $parentClassName);
        /** @var AbstractClassTrait|AbstractClass $object */
        $object = $classMock->newInstanceWithoutConstructor();

        $this->assertSame('bar', $object->bar());

        $classMock->mockMethod('bar')->set(function () {
            return 'foo';
        });
        $this->assertSame('foo', $object->bar());
    }

    public function testUnmockMethod() {
        $parentClassName = '\\MockaMocks\\AbstractClass';
        $classMock = new ClassMock(null, $parentClassName);
        /** @var AbstractClassTrait|AbstractClass $object */
        $object = $classMock->newInstanceWithoutConstructor();

        $this->assertSame('bar', $object->bar());

        $classMock->mockMethod('bar');
        $this->assertSame(null, $object->bar());

        $classMock->unmockMethod('bar');
        $this->assertSame('bar', $object->bar());
    }

    /**
     * @expectedException \Mocka\Exception
     */
    public function testMockMethodFinal() {
        $classMock = new ClassMock(null, '\\MockaMocks\\AbstractClass');
        $classMock->mockMethod('zoo');
    }

    public function testMockMethodFromTrait() {
        $classMock = new ClassMock(null, '\\MockaMocks\\AbstractClass', null, ['\\MockaMocks\\TraitMock']);

        /** @var AbstractClassTrait|AbstractClass $object */
        $object = $classMock->newInstanceWithoutConstructor();
        $this->assertSame('traitbar', $object->bar());

        $classMock->mockMethod('bar');
        $this->assertSame(null, $object->bar());
    }

    public function testMockStaticMethod() {
        $classMock = new ClassMock(null, '\\MockaMocks\\AbstractClass');
        /** @var AbstractClass $className */
        $className = $classMock->getClassName();

        $this->assertSame('jar', $className::jar());
        $classMock->mockStaticMethod('jar')->set(function () {
            return 'foo';
        });
        $this->assertSame('foo', $className::jar());

        $classMock->mockStaticMethod('nonexistent')->set(function () {
            return 'bar';
        });
        $this->assertSame('bar', $className::nonexistent());
    }

    public function testNewInstanceConstructorArgs() {
        $classMock = new ClassMock(null, '\\MockaMocks\\AbstractClass');
        $constructorArgs = ['foo', 'bar'];
        /** @var AbstractClass $object */
        $object = $classMock->newInstance($constructorArgs);
        $this->assertSame($object->constructorArgs, $constructorArgs);
    }

    public function testNewInstanceWithoutConstructor() {
        $classMock = new ClassMock();
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
