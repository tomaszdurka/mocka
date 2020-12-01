<?php

namespace MockaMocks;

abstract class AbstractClass implements InterfaceMock{

    public $constructorArgs;

    abstract public function foo();

    public function __construct($arg1, $arg2 = null) {
        $this->constructorArgs = array($arg1, $arg2);
        $this->_foo();
    }

    public function bar() {
        return static::jar();
    }

    public final function zoo() {
    }

    public function getCalledClass() {
        return get_called_class();
    }

    public function fooReturn(): string {
        return 'foo';
    }

    public function fooReturnOptional(): ?float {
        return 2.3;
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
