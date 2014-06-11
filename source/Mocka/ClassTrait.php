<?php

namespace Mocka;

trait ClassTrait {

    /** @var ClassMock */
    private static $_classMock;

    /**
     * @param string $methodName
     * @param array  $arguments
     * @return mixed
     */
    public function __call($methodName, $arguments) {
        return $this->_callMethod($methodName, $arguments);
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
        $reflectionClass = new \ReflectionClass($this);
        $method = $reflectionClass->getParentClass()->getMethod($name);
        if (!$method->isAbstract()) {
            return $method->invoke($this, $arguments);
        }
    }

    /**
     * @param ClassMock $classMock
     */
    public static function setMockClass(ClassMock $classMock) {
        self::$_classMock = $classMock;
    }
}
