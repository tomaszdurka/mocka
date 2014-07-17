<?php

namespace Mocka;

trait ClassTrait {

    /** @var ClassMock */
    private static $_classMock;

    /** @var ClassMock */
    private $_objectClassMock;

    public function __clone() {
        $this->_objectClassMock = clone $this->_objectClassMock;
        if (static::_hasParentMethod('__clone')) {
            call_user_func(array('parent', '__clone'));
        }
    }

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
        if (static::_hasParentMethod($name)) {
            return call_user_func_array(array('parent', $name), $arguments);
        }
        return null;
    }

    /**
     * @param string $name
     * @return bool
     */
    private static function _hasParentMethod($name) {
        $className = get_called_class();
        $reflectionParentClass = (new \ReflectionClass($className))->getParentClass();
        if (!$reflectionParentClass) {
            return false;
        }
        if (!$reflectionParentClass->hasMethod($name)) {
            return false;
        }
        $reflectionMethod = $reflectionParentClass->getMethod($name);
        if ($reflectionMethod->isAbstract() || $reflectionMethod->isPrivate()) {
            return false;
        }
        return true;
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
        if (static::_hasParentMethod($name)) {
            return call_user_func_array(array('parent', $name), $arguments);
        }
        return null;
    }
}
