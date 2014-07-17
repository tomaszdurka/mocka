<?php

namespace Mocka;

trait ClassTrait {

    /** @var ClassMock */
    private static $_classMock;

    /** @var ClassMock */
    private $_objectClassMock;

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
        return $this->_getObjectClassMock()->mockMethod($name);
    }

    /**
     * @return ClassMock
     */
    private function _getObjectClassMock() {
        if (!$this->_objectClassMock) {
            $this->_objectClassMock = new ClassMock();
        }
        return $this->_objectClassMock;
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    private function _callMethod($name, array $arguments) {
        if ($this->_getObjectClassMock()->hasMockedMethod($name)) {
            return $this->_getObjectClassMock()->callMockedMethod($name, $arguments);
        }
        if (self::$_classMock->hasMockedMethod($name)) {
            return self::$_classMock->callMockedMethod($name, $arguments);
        }

        $reflectionParentClass = (new \ReflectionClass($this))->getParentClass();
        if ($reflectionParentClass->hasMethod($name)) {
            $reflectionMethod = (new \ReflectionClass($this))->getParentClass()->getMethod($name);
            if (!$reflectionMethod->isAbstract() && !$reflectionMethod->isPrivate()) {
                return call_user_func_array(array('parent', $name), $arguments);
            }
        }
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments) {
        return static::_callStaticMethod($name, $arguments);
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
    private static function _callStaticMethod($name, array $arguments) {
        if (self::$_classMock->hasMockedStaticMethod($name)) {
            return self::$_classMock->callMockedStaticMethod($name, $arguments);
        }
        $reflectionMethod = (new \ReflectionClass(get_called_class()))->getParentClass()->getMethod($name);
        if (!$reflectionMethod->isAbstract() && !$reflectionMethod->isPrivate()) {
            return call_user_func_array(array('parent', $name), $arguments);
        }
    }
}
