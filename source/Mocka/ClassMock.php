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
     * @return object
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

        $class = new \CG_Class($this->getName(), $this->_parentClassName);
        $class->setNamespace($this->getNamespace());
        $class->addUse('\Mocka\ClassTrait');

        $reflection = new \ReflectionClass($this->_parentClassName);
        foreach ($reflection->getMethods() as $reflectionMethod) {
            $method = \CG_Method::buildFromReflection($reflectionMethod);
            $method->setAbstract(false);
            $method->extractFromClosure(function () {
                return $this->_callMethod(__FUNCTION__, func_get_args());
            });
            $class->addMethod($method);
        };
        return $class->dump();
    }

    private function _load() {
        $code = $this->generateCode();
        eval($code);
    }
}
