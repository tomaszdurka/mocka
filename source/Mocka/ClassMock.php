<?php

namespace Mocka;

class ClassMock {

    /** @var string */
    private $_className;

    /** @var string */
    private $_name;

    /** @var string */
    private $_namespace;

    /** @var string */
    private $_parentClassName;

    /** @var MethodMock[] */
    private $_mockedMethods = array();

    /**
     * @param string $className
     */
    public function __construct($className) {
        $this->_className = $className . uniqid();
        $parts = array_filter(explode('\\', $this->_className));
        $this->_name = array_pop($parts);
        $this->_namespace = join('\\', $parts);
        $this->_parentClassName = (string) $className;

        $this->_load();
    }

    /**
     * @return string
     */
    public function getClassName() {
        return $this->_className;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * @return string
     */
    public function getNamespace() {
        return $this->_namespace;
    }

    /**
     * @param array|null $constructorArgs
     * @return \Mocka\ClassTrait
     */
    public function newInstance(array $constructorArgs = null) {
        $constructorArgs = (array) $constructorArgs;
        $mockedClassReflection = new \ReflectionClass($this->getClassName());
        /** @var ClassTrait $instance */
        $instance = $mockedClassReflection->newInstanceArgs($constructorArgs);
        $instance->setMockClass($this);
        return $instance;
    }

    /**
     * @return string
     */
    public function generateCode() {

        $class = new \CG_Class($this->getName(), $this->_parentClassName);
        $class->setNamespace($this->getNamespace());
        $class->addUse('\Mocka\ClassTrait');

        $reflectionTrait = new \ReflectionClass('\\Mocka\\ClassTrait');
        $traitMethods = array_map(function (\ReflectionMethod $method) {
            return $method->getName();
        }, $reflectionTrait->getMethods());
        $reflectionClass = new \ReflectionClass($this->_parentClassName);
        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            if ($reflectionMethod->isPrivate() || $reflectionMethod->isFinal() || in_array($reflectionMethod->getName(), $traitMethods)) {
                continue;
            }
            $method = new \CG_Method($reflectionMethod->getName());
            $method->setAbstract(false);
            $method->setParametersFromReflection($reflectionMethod);
            $method->setStaticFromReflection($reflectionMethod);
            $method->setVisibilityFromReflection($reflectionMethod);
            $method->extractFromClosure(function () {
                return $this->_callMethod(__FUNCTION__, func_get_args());
            });
            $class->addMethod($method);
        };
        return $class->dump();
    }

    /**
     * @param string $name
     * @return MethodMock
     */
    public function mockMethod($name) {
        $this->_mockedMethods[$name] = new MethodMock();
        return $this->_mockedMethods[$name];
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


    private function _load() {
        $code = $this->generateCode();
        eval($code);
    }
}
