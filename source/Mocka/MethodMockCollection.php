<?php

namespace Mocka;

class MethodMockCollection {

    /** @var MethodMock[] */
    private $_mockedMethods = array();

    /**
     * @param string $name
     * @return MethodMock
     */
    public function mockMethod($name) {
        $name = (string) $name;
        $this->_mockedMethods[$name] = new MethodMock();
        return $this->_mockedMethods[$name];
    }

    /**
     * @param string $name
     * @throws Exception
     */
    public function unmockMethod($name) {
        $name = (string) $name;
        if (!$this->hasMockedMethod($name)) {
            throw new Exception("Method `{$name}` has not been mocked");
        }
        unset($this->_mockedMethods[$name]);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasMockedMethod($name) {
        return array_key_exists($name, $this->_mockedMethods);
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    public function callMockedMethod($name, $arguments) {
        $method = $this->_mockedMethods[$name];
        return $method->invoke($arguments);
    }
}
