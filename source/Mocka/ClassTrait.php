<?php

namespace Mocka;

trait ClassTrait {

    /** @var ClassMock */
    private $_classMock;

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
        return $this->_classMock->mockMethod($name);
    }

    /**
     * @param ClassMock $classMock
     */
    public function setMockClass(ClassMock $classMock) {
        $this->_classMock = $classMock;
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    private function _callMethod($name, array $arguments) {
        if ($this->_classMock->hasMockedMethod($name)) {
            return $this->_classMock->callMockedMethod($name, $arguments);
        }
        $reflectionClass = new \ReflectionClass($this);
        $method = $reflectionClass->getParentClass()->getMethod($name);
        if (!$method->isAbstract()) {
            return $method->invoke($this, $arguments);
        }
    }
}
