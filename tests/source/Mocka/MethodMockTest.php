<?php

namespace MockaTests;

use Mocka\MethodMock;

class MethodMockTest extends \PHPUnit_Framework_TestCase {

    public function testIntegrated() {
        $method = new MethodMock();
        $method->set(function () {
            return 'foo';
        });

        $method->at(1, function() {
            return 'bar';
        });

        $this->assertSame('foo', $method->invoke());
        $this->assertSame('bar', $method->invoke());
        $this->assertSame('foo', $method->invoke());
    }

}
