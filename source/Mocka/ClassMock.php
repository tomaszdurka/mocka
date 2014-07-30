<?php

namespace Mocka;

use CodeGenerator\ClassBlock;
use CodeGenerator\PropertyBlock;

class ClassMock {

    /** @var string */
    private $_name;

    /** @var string|null */
    private $_namespace;

    /** @var string|null */
    private $_parentClassName;

    /** @var MethodMockCollection */
    private $_methodMockCollectionInstance;

    /** @var MethodMockCollection */
    private $_methodMockCollectionStatic;

    /**
     * @param string|null $className
     * @param string|null $parentClassName
     * @param array|null  $interfaces
     */
    public function __construct($className = null, $parentClassName = null, array $interfaces = null) {
        $this->_parentClassName = ClassAbstractMock::getClassName($parentClassName, (array) $interfaces);
        if (null === $className) {
            $className = $parentClassName . 'Mocka' . uniqid();
        }
        $this->_extractNameAndNamespace($className);
        $this->_methodMockCollectionInstance = new MethodMockCollection();
        $this->_methodMockCollectionStatic = new MethodMockCollection();

        $this->_load();
    }

    /**
     * @return string
     */
    public function getClassName() {
        $className = '\\';
        if ($this->_namespace) {
            $className .= $this->_namespace . '\\';
        }
        $className .= $this->_name;
        return $className;
    }

    /**
     * @return string
     */
    public function getParentClassName() {
        return $this->_parentClassName;
    }

    /**
     * @return string|null
     */
    public function getMockedClassName() {
        if (!$this->_parentClassName) {
            return null;
        }
        return get_parent_class($this->_parentClassName);
    }

    /**
     * @param array|null $constructorArgs
     * @return \Mocka\AbstractClassTrait
     */
    public function newInstance(array $constructorArgs = null) {
        $constructorArgs = (array) $constructorArgs;
        $mockedClassReflection = new \ReflectionClass($this->getClassName());
        return $mockedClassReflection->newInstanceArgs($constructorArgs);
    }

    /**
     * @return \Mocka\AbstractClassTrait
     */
    public function newInstanceWithoutConstructor() {
        $mockedClassReflection = new \ReflectionClass($this->getClassName());
        return $mockedClassReflection->newInstanceWithoutConstructor();
    }

    /**
     * @return string
     */
    public function generateCode() {
        $class = new ClassBlock($this->_name);
        if ($this->_namespace) {
            $class->setNamespace($this->_namespace);
        }
        if ($this->_parentClassName) {
            $class->setParentClassName($this->_parentClassName);
        }
        $class->addUse('\Mocka\ClassTrait');
        return $class->dump();
    }

    /**
     * @param string $name
     * @throws Exception
     * @return MethodMock
     */
    public function mockMethod($name) {
        if ($this->_parentClassName) {
            $reflectionClass = new \ReflectionClass($this->_parentClassName);
            if ($reflectionClass->hasMethod($name) && $reflectionClass->getMethod($name)->isFinal()) {
                throw new Exception('Cannot mock final method `' . $name . '`');
            }
        }
        return $this->_methodMockCollectionInstance->mockMethod($name);
    }

    /**
     * @param string $name
     */
    public function unmockMethod($name) {
        $this->_methodMockCollectionInstance->unmockMethod($name);
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
        return $this->_methodMockCollectionStatic->mockMethod($name);
    }

    /**
     * @param string $name
     */
    public function unmockStaticMethod($name) {
        $this->_methodMockCollectionStatic->unmockMethod($name);
    }

    /**
     * @return MethodMockCollection
     */
    public function getMethodMockCollectionInstance() {
        return $this->_methodMockCollectionInstance;
    }

    /**
     * @return MethodMockCollection
     */
    public function getMethodMockCollectionStatic() {
        return $this->_methodMockCollectionStatic;
    }

    private function _load() {
        $code = $this->generateCode();
        eval($code);
        /** @var AbstractClassTrait $className */
        $className = $this->getClassName();
        $className::setMockClass($this);
    }

    /**
     * @param string $className
     */
    private function _extractNameAndNamespace($className) {
        $parts = explode('\\', $className);
        $parts = array_filter($parts);
        $this->_name = array_pop($parts);
        if ($parts) {
            $this->_namespace = join('\\', $parts);
        }
    }
}
