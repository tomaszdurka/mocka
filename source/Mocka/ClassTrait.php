<?php

namespace Mocka;

trait ClassTrait {

    /** @var MethodMock */
    private $_mockedMethods;

    /**
     * @param string $name
     * @return MethodMock
     */
    public function mockMethod($name) {
        $this->_mockedMethods[$name] = new MethodMock();
        return $this->_mockedMethods[$name];
    }

    /**
     * @param string $methodName
     * @param array $arguments
     * @return mixed
     */
    public function __call($methodName, $arguments) {
        return $this->_callMethod($methodName, $arguments);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    private function _callMethod($name, array $arguments) {
        if ($this->_hasMockedMethod($name)) {
            return $this->_callMockedMethod($name, $arguments);
        }
        return call_user_func_array(array('parent', $name), $arguments);
    }

    /**
     * @param string $name
     * @return bool
     */
    private function _hasMockedMethod($name) {
        return array_key_exists($name, $this->_mockedMethods);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    private function _callMockedMethod($name, $arguments) {
        $method = $this->_mockedMethods[$name];
        return call_user_func_array($method, $arguments);
    }

}
