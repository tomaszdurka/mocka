<?php

namespace MockaTests;

use Mocka\Invokable\Stub;
use Mocka\Mocka;

class StubTest extends \PHPUnit_Framework_TestCase {

    public function testIntegrated() {
        $method = new Stub();
        $method->set(function () {
            return 'foo';
        });
        $method->at(1, function () {
            return 'bar';
        });
        $method->at([2, 5], function () {
            return 'zoo';
        });

        $this->assertSame('foo', $method->invoke('context', []));
        $this->assertSame('bar', $method->invoke('context', []));
        $this->assertSame('zoo', $method->invoke('context', []));
        $this->assertSame('foo', $method->invoke('context', []));

        $method->set(function () {
            return 'def';
        });
        $this->assertSame('def', $method->invoke('context', []));
        $this->assertSame('zoo', $method->invoke('context', []));
    }

    public function testAssertingArguments() {
        $method = new Stub();
        $method->set(function($foo) {
            $this->assertSame('bar', $foo);
        });
        $method->invoke('context', ['bar']);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage must be an instance of Mocka
     */
    public function testTypeHinting() {
        $method = new Stub();
        $method->set(function (Mocka $mocka) {
        });
        $method->invoke('context',['Invalid arg']);
    }

    public function testGetCallCount() {
        $method = new Stub();
        $this->assertSame(0, $method->getCallCount());
        $method->invoke('context', []);
        $this->assertSame(1, $method->getCallCount());
    }
}
