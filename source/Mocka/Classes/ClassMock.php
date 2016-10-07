<?php

namespace Mocka\Classes;

use CodeGenerator\ClassBlock;
use Mocka\Exception;
use Mocka\Invokables\Invokable\Stub;
use Mocka\Overrides\Manager;
use Mocka\Overrides\MethodOverrides\ClassOverrides;

class ClassMock {

    /** @var string */
    private $_name;

    /** @var string|null */
    private $_namespace;

    /** @var string|null */
    private $_parentClassName;

    /**
     * ClassMock constructor.
     * @param string      $wrapperClassName
     * @param string|null $className
     */
    public function __construct($wrapperClassName, $className = null) {
        $this->_parentClassName = $wrapperClassName;
        if (null === $className) {
            $className = $wrapperClassName . '\\Mocka' . uniqid();
        }
        $this->_extractNameAndNamespace($className);
    }

    /**
     * @return string
     */
    public function getClassName() {
        $className = '';
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
        return get_parent_class($this->_parentClassName);
    }

    /**
     * @param array|null $constructorArgs
     * @return ClassMockTrait
     */
    public function newInstance(array $constructorArgs = null) {
        $constructorArgs = (array) $constructorArgs;
        $mockedClassReflection = new \ReflectionClass($this->getClassName());
        return $mockedClassReflection->newInstanceArgs($constructorArgs);
    }

    /**
     * @return ClassMockTrait
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
        return $class->dump();
    }

    /**
     * @param string $name
     * @throws Exception
     * @return Stub
     */
    public function mockMethod($name) {
        $manager = Manager::getInstance();
        $classOverrides = new ClassOverrides($manager, $this->getClassName());
        return $classOverrides->stub($name);
    }

    /**
     * @param string $name
     */
    public function unmockMethod($name) {
        $manager = Manager::getInstance();
        $classOverrides = new ClassOverrides($manager, $this->getClassName());
        $classOverrides->remove($name);
    }
    
    

    public function load() {
        $code = $this->generateCode();
        eval($code);
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
