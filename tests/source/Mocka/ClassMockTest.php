<?php

namespace MockaTests\Mocka;

use Mocka\Classes\ClassMock;
use Mocka\Classes\ClassMockFactory;
use Mocka\Classes\OverridableTrait;
use MockaMocks\AbstractClass;
use PHPUnit\Framework\TestCase;

class ClassMockTest extends TestCase {

    public function testGenerateCode() {
        $parentClassName = '\\MockaMocks\\AbstractClass';

        $classMock = new ClassMock($parentClassName, '\\Nested\\FooNamespace\\FooClass');
        $parentMockClassName = $classMock->getParentClassName();
        $expectedMockCode = <<<EOD
namespace Nested\\FooNamespace;

class FooClass extends $parentMockClassName implements \Mocka\Overrides\OverridableInterface {
}
EOD;
        $this->assertSame($expectedMockCode, $classMock->generateCode());
    }

    public function testMockMethod() {
        $factory = new ClassMockFactory();
        $classMock = $factory->loadClassMock(null, '\\MockaMocks\\AbstractClass');

        /** @var OverridableTrait|AbstractClass $object */
        $object = $classMock->newInstanceWithoutConstructor();

        $this->assertSame('jar', $object->bar());

        $classMock->mockMethod('bar')->set(function () {
            return 'foo';
        });
        $this->assertSame('foo', $object->bar());
    }

    public function testMockReturnTypeMethod() {
        $factory = new ClassMockFactory();
        $classMock = $factory->loadClassMock(null, '\\MockaMocks\\AbstractClass');

        /** @var OverridableTrait|AbstractClass $object */
        $object = $classMock->newInstanceWithoutConstructor();

        $this->assertSame('foo', $object->fooReturn());

        $classMock->mockMethod('fooReturn')->set(function () {
            return 'fooBar';
        });
        $this->assertSame('fooBar', $object->fooReturn());

        $classMock->mockMethod('fooReturn')->set(function (): string {
            return 'fooBarString';
        });
        $this->assertSame('fooBarString', $object->fooReturn());


        $classMock->mockMethod('fooReturn')->set(function (): int {
            return 234;
        });
        $this->assertSame('234', $object->fooReturn()); //casts to string
    }

    public function testMockReturnTypeMethodInvalidWrongReturnType() {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('must be of the type integer');
        $factory = new ClassMockFactory();
        $classMock = $factory->loadClassMock(null, '\\MockaMocks\\AbstractClass');

        /** @var OverridableTrait|AbstractClass $object */
        $object = $classMock->newInstanceWithoutConstructor();

        $this->assertSame('foo', $object->fooReturn());

        $classMock->mockMethod('fooReturn')->set(function (): int {
            return 'fooBar123';
        });
        $object->fooReturn();
    }

    public function testMockReturnTypeMethodInvalidNull() {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('must be of the type string, null returned');
        $factory = new ClassMockFactory();
        $classMock = $factory->loadClassMock(null, '\\MockaMocks\\AbstractClass');

        /** @var OverridableTrait|AbstractClass $object */
        $object = $classMock->newInstanceWithoutConstructor();

        $this->assertSame('foo', $object->fooReturn());

        $classMock->mockMethod('fooReturn')->set(function () {
            return null;
        });
        $object->fooReturn();
    }

    public function testMockReturnTypeOptionalMethod() {
        $factory = new ClassMockFactory();
        $classMock = $factory->loadClassMock(null, '\\MockaMocks\\AbstractClass');

        /** @var OverridableTrait|AbstractClass $object */
        $object = $classMock->newInstanceWithoutConstructor();

        $this->assertSame(2.3, $object->fooReturnOptional());

        $classMock->mockMethod('fooReturnOptional')->set(function () {
            return 1.1;
        });
        $this->assertSame(1.1, $object->fooReturnOptional());

        $classMock->mockMethod('fooReturnOptional')->set(function () {
            return null;
        });
        $this->assertNull($object->fooReturnOptional());
    }

    public function testUnmockMethod() {
        $factory = new ClassMockFactory();
        $classMock = $factory->loadClassMock(null, '\\MockaMocks\\AbstractClass');

        /** @var OverridableTrait|AbstractClass $object */
        $object = $classMock->newInstanceWithoutConstructor();

        $this->assertSame('jar', $object->bar());

        $classMock->mockMethod('bar');
        $this->assertSame(null, $object->bar());

        $classMock->unmockMethod('bar');
        $this->assertSame('jar', $object->bar());
    }

    public function testMockMethodFromTrait() {
        $factory = new ClassMockFactory();
        $classMock = $factory->loadClassMock(null, '\\MockaMocks\\AbstractClass', null, ['\\MockaMocks\\TraitMock']);

        /** @var OverridableTrait|AbstractClass $object */
        $object = $classMock->newInstanceWithoutConstructor();
        $this->assertSame('traitbar', $object->bar());

        $classMock->mockMethod('bar');
        $this->assertSame(null, $object->bar());
    }

    public function testGetCalledClass() {
        $factory = new ClassMockFactory();
        $classMock = $factory->loadClassMock(null, '\\MockaMocks\\AbstractClass');
        /** @var AbstractClass $object */
        $object = $classMock->newInstanceWithoutConstructor();

        $this->assertSame($classMock->getClassName(), $object->getCalledClass());
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

    public function testMockStaticMethodCalledFromOther() {
        $factory = new ClassMockFactory();
        $classMock = $factory->loadClassMock(null, '\\MockaMocks\\AbstractClass');
        /** @var AbstractClass $object */
        $object = $classMock->newInstanceWithoutConstructor();

        $this->assertSame('jar', $object->bar());
        $classMock->mockMethod('jar')->set(function () {
            return 'foo';
        });
        $this->assertSame('foo', $object->bar());
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
        $classMock = new ClassMock(null, 'DateTime');
        $this->assertInstanceOf(ClassMock::class, $classMock);
    }
}
