<?php

namespace Mocka;

trait AbstractClassTrait {

    /** @var MethodMockCollection */
    private $_objectMethodMockCollection;

    public function __clone() {
        $this->_objectMethodMockCollection = clone $this->_objectMethodMockCollection;
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
        return $this->_getObjectMethodMockCollection()->mockMethod($name);
    }

    /**
     * @return MethodMockCollection
     */
    private function _getObjectMethodMockCollection() {
        if (!$this->_objectMethodMockCollection) {
            $this->_objectMethodMockCollection = new MethodMockCollection();
        }
        return $this->_objectMethodMockCollection;
    }

    /**
     * @return MethodMockCollection
     */
    private function _getClassMethodMockCollection() {
        return static::_getMockClass()->getMethodMockCollectionInstance();
    }

    /**
     * @return MethodMockCollection
     */
    private static function _getClassMethodMockCollectionStatic() {
        return static::_getMockClass()->getMethodMockCollectionStatic();
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    private function _callMethod($name, array $arguments) {
        if ($this->_getObjectMethodMockCollection()->hasMockedMethod($name)) {
            return $this->_getObjectMethodMockCollection()->callMockedMethod($name, $arguments);
        }
        if ($this->_getClassMethodMockCollection()->hasMockedMethod($name)) {
            return $this->_getClassMethodMockCollection()->callMockedMethod($name, $arguments);
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
        $mockedClassName = static::_getMockClass()->getMockedClassName();
        if (!$mockedClassName) {
            return false;
        }
        $reflectionParentClass = new \ReflectionClass($mockedClassName);
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
     * @throws Exception
     * @return ClassMock
     */
    protected static function _getMockClass() {
        throw new Exception('Not implemented');
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    private static function _callStaticMethod($name, array $arguments) {
        if (static::_getClassMethodMockCollectionStatic()->hasMockedMethod($name)) {
            return static::_getClassMethodMockCollectionStatic()->callMockedMethod($name, $arguments);
        }
        if (static::_hasParentMethod($name)) {
            return call_user_func_array(array('parent', $name), $arguments);
        }
        return null;
    }
}