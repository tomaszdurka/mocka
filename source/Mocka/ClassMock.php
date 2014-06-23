<?php

namespace Mocka;

use CodeGenerator\ClassBlock;
use CodeGenerator\MethodBlock;

class ClassMock {

    /** @var string */
    private $_className;

    /** @var string|null */
    private $_parentClassName;

    /** @var array */
    private $_interfaces;

    /** @var MethodMock[] */
    private $_mockedMethods = array();

    /** @var MethodMock[] */
    private $_mockedStaticMethods = array();

    /**
     * @param string     $className
     * @param array|null $interfaces
     */
    public function __construct($className, array $interfaces = null) {
        $this->_className = 'Mocka' . uniqid();
        if (null !== $className) {
            $this->_parentClassName = (string) $className;
        }
        $this->_interfaces = (array) $interfaces;

        $this->_load();
    }

    /**
     * @return string
     */
    public function getClassName() {
        return $this->_className;
    }

    /**
     * @param array|null $constructorArgs
     * @return \Mocka\ClassTrait
     */
    public function newInstance(array $constructorArgs = null) {
        $constructorArgs = (array) $constructorArgs;
        $mockedClassReflection = new \ReflectionClass($this->getClassName());
        return $mockedClassReflection->newInstanceArgs($constructorArgs);
    }

    /**
     * @return string
     */
    public function generateCode() {
        $class = new ClassBlock($this->getClassName());
        if ($this->_parentClassName) {
            $class->setParentClassName($this->_parentClassName);
        }
        foreach ($this->_interfaces as $interface) {
            $class->addInterface($interface);
        }
        $class->addUse('\Mocka\ClassTrait');

        foreach ($this->_getMockableMethods() as $reflectionMethod) {
            $method = new MethodBlock($reflectionMethod->getName());
            $method->setAbstract(false);
            $method->setParametersFromReflection($reflectionMethod);
            $method->setStaticFromReflection($reflectionMethod);
            $method->setVisibilityFromReflection($reflectionMethod);
            if ($reflectionMethod->isStatic()) {
                $method->extractFromClosure(function () {
                    return static::_callStaticMethod(__FUNCTION__, func_get_args());
                });
            } else {
                $method->extractFromClosure(function () {
                    return $this->_callMethod(__FUNCTION__, func_get_args());
                });
            }
            $class->addMethod($method);
        }
        return $class->dump();
    }

    /**
     * @param string $name
     * @throws Exception
     * @return MethodMock
     */
    public function mockMethod($name) {
        $reflectionClass = new \ReflectionClass($this->_parentClassName);
        if ($reflectionClass->hasMethod($name)) {
            if ($reflectionClass->getMethod($name)->isFinal()) {
                throw new Exception('Cannot mock final method `' . $name . '`');
            }
        }
        $this->_mockedMethods[$name] = new MethodMock();
        return $this->_mockedMethods[$name];
    }

    /**
     * @param string $name
     * @throws Exception
     * @return MethodMock
     */
    public function mockStaticMethod($name) {
        $reflectionClass = new \ReflectionClass($this->_parentClassName);
        if ($reflectionClass->hasMethod($name)) {
            if ($reflectionClass->getMethod($name)->isFinal()) {
                throw new Exception('Cannot mock final method `' . $name . '`');
            }
        }
        $this->_mockedStaticMethods[$name] = new MethodMock();
        return $this->_mockedStaticMethods[$name];
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
     * @return bool
     */
    public function hasMockedStaticMethod($name) {
        return array_key_exists($name, $this->_mockedStaticMethods);
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

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    public function callMockedStaticMethod($name, $arguments) {
        $method = $this->_mockedStaticMethods[$name];
        return $method->invoke($arguments);
    }

    private function _load() {
        $code = $this->generateCode();
        eval($code);
        /** @var ClassTrait $className */
        $className = $this->getClassName();
        $className::setMockClass($this);
    }

    /**
     * @return \ReflectionMethod[]
     */
    private function _getMockableMethods() {
        /** @var \ReflectionMethod[] $methods */
        $methods = array();
        $parents = $this->_interfaces + (array) $this->_parentClassName;
        foreach ($parents as $parent) {
            $reflectionClass = new \ReflectionClass($parent);
            foreach ($reflectionClass->getMethods() as $method) {
                $methods[$method->getName()] = $method;
            }
        }

        $reflectionTrait = new \ReflectionClass('\\Mocka\\ClassTrait');
        return array_filter($methods, function (\ReflectionMethod $reflectionMethod) use ($reflectionTrait) {
            return !$reflectionMethod->isPrivate() && !$reflectionMethod->isFinal() && !$reflectionTrait->hasMethod($reflectionMethod->getName());
        });
    }
}
