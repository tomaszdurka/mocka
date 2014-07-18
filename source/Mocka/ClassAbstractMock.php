<?php

namespace Mocka;

use CodeGenerator\ClassBlock;
use CodeGenerator\MethodBlock;

class ClassAbstractMock {

    /** @var string */
    private $_className;

    /** @var string|null */
    private $_parentClassName;

    /** @var array */
    private $_interfaces;

    /** @var ClassAbstractMock[] */
    private static $_mocks = [];

    /**
     * @param string|null $parentClassName
     * @param array       $interfaces
     */
    public function __construct($parentClassName, array $interfaces) {
        $this->_className = 'MockaAbstractClass' . uniqid();
        if (null !== $parentClassName) {
            $this->_parentClassName = (string) $parentClassName;
        }
        $this->_interfaces = $interfaces;
        $this->_load();
    }

    /**
     * @return string
     */
    public function generateCode() {
        $class = new ClassBlock($this->_className);
        if ($this->_parentClassName) {
            $class->setParentClassName($this->_parentClassName);
        }
        foreach ($this->_interfaces as $interface) {
            $class->addInterface($interface);
        }
        $class->addUse('\Mocka\AbstractClassTrait');

        $mockableMethods = $this->_getMockableMethods();
        foreach ($mockableMethods as $reflectionMethod) {
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
        if (!array_key_exists('__construct', $mockableMethods)) {
            $method = new MethodBlock('__construct');
            $method->extractFromClosure(function () {
                return $this->_callMethod(__FUNCTION__, func_get_args());
            });
            $class->addMethod($method);
        }
        return $class->dump();
    }

    /**
     * @return string
     */
    protected function _getClassName() {
        return $this->_className;
    }

    private function _load() {
        $code = $this->generateCode();
        eval($code);
    }

    /**
     * @return \ReflectionMethod[]
     */
    private function _getMockableMethods() {
        /** @var \ReflectionMethod[] $methods */
        $methods = array();
        $interfaces = $this->_interfaces;
        if ($this->_parentClassName) {
            $interfaces[] = $this->_parentClassName;
        }
        foreach ($interfaces as $interface) {
            $reflectionClass = new \ReflectionClass($interface);
            foreach ($reflectionClass->getMethods() as $method) {
                $methods[$method->getName()] = $method;
            }
        }

        $reflectionTrait = new \ReflectionClass('\\Mocka\\ClassTrait');
        $methods = array_filter($methods, function (\ReflectionMethod $reflectionMethod) use ($reflectionTrait) {
            return !$reflectionMethod->isPrivate() && !$reflectionMethod->isFinal() && !$reflectionTrait->hasMethod($reflectionMethod->getName());
        });
        return $methods;
    }

    /**
     * @param string $parentClassName
     * @param array  $interfaces
     * @return string|null
     */
    public static function getClassName($parentClassName, array $interfaces) {
        sort($interfaces);
        $mergedInterfaces = array_merge([$parentClassName], $interfaces);
        $mergedInterfaces = array_filter($mergedInterfaces);
        $hash = join(',', $mergedInterfaces);
        if (!array_key_exists($hash, self::$_mocks)) {
            self::$_mocks[$hash] = new self($parentClassName, $interfaces);
        }
        return self::$_mocks[$hash]->_className;
    }
}
