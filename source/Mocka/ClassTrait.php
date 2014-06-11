<?php

namespace Mocka;

trait ClassTrait {

    /** @var ClassMock */
    private static $_classMock;

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    public function __call($name, $arguments) {
        return $this->_callMethod($name, $arguments);
    }

    /**
     * @param string $name
     * @return MethodMock
     */
    public function mockMethod($name) {
        return self::$_classMock->mockMethod($name);
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    private function _callMethod($name, array $arguments) {
        if (self::$_classMock->hasMockedMethod($name)) {
            return self::$_classMock->callMockedMethod($name, $arguments);
        }
        $reflectionMethod = (new \ReflectionClass($this))->getParentClass()->getMethod($name);
        if (!$reflectionMethod->isAbstract()) {
            return $reflectionMethod->invokeArgs($this, $arguments);
        }
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments) {
        return static::_callMethodStatic($name, $arguments);
    }

    /**
     * @param ClassMock $classMock
     */
    public static function setMockClass(ClassMock $classMock) {
        self::$_classMock = $classMock;
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    private static function _callMethodStatic($name, array $arguments) {
        if (self::$_classMock->hasMockedMethod($name)) {
            return self::$_classMock->callMockedMethod($name, $arguments);
        }
        $reflectionMethod = (new \ReflectionClass(get_called_class()))->getParentClass()->getMethod($name);
        if (!$reflectionMethod->isAbstract()) {
            return $reflectionMethod->invokeArgs(null, $arguments);
        }
    }
}
