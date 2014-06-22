<?php

namespace MockaMocks;

abstract class AbstractClass implements InterfaceMock{

    public $constructorArgs;

    abstract public function foo();

    public function __construct($arg1 = null, $arg2 = null) {
        $this->constructorArgs = array($arg1, $arg2);
        $this->_foo();
    }

    public function bar() {
        return 'bar';
    }

    public final function zoo() {
    }

    protected function _foo(){
    }

    public static function jar() {
        return static::_jar();
    }

    protected static function _jar() {
        return 'jar';
    }
}
