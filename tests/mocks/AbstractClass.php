<?php

namespace MockaMocks;

abstract class AbstractClass {

    public $constructorArgs;

    abstract public function foo();

    public function __construct($arg1 = null, $arg2 = null) {
        $this->constructorArgs = array($arg1, $arg2);
    }

    public function bar() {
        return 'bar';
    }

    public final function zoo() {
    }
}
