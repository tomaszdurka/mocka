<?php

namespace MockaTests;

use Mocka\FunctionMock;

class FunctionMockTest extends \PHPUnit_Framework_TestCase {

    public function testIntegrated() {
        $method = new FunctionMock();
        $method->set(function () {
            return 'foo';
        });
        $method->at(1, function () {
            return 'bar';
        });
        $method->at([2, 5], function () {
            return 'zoo';
        });

        $this->assertSame('foo', $method->invoke());
        $this->assertSame('bar', $method->invoke());
        $this->assertSame('zoo', $method->invoke());
        $this->assertSame('foo', $method->invoke());

        $method->set(function () {
            return 'def';
        });
        $this->assertSame('def', $method->invoke());
        $this->assertSame('zoo', $method->invoke());
    }

    public function testAssertingArguments() {
        $method = new FunctionMock();
        $method->set(function($foo) {
            $this->assertSame('bar', $foo);
        });
        $method->invoke(['bar']);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage must be an instance of Mocka
     */
    public function testTypeHinting() {
        $method = new FunctionMock();
        $method->set(function (\Mocka $mocka) {
        });
        $method->invoke(['Invalid arg']);
    }

    public function testGetCallCount() {
        $method = new FunctionMock();
        $this->assertSame(0, $method->getCallCount());
        $method->invoke();
        $this->assertSame(1, $method->getCallCount());
    }

    public function testCallable() {
        $method = new FunctionMock();
        $method->set('foo');
        $this->assertSame('foo', $method());
    }
}
