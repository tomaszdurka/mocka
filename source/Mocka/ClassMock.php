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

    public function __construct($className) {
        $this->_className = $className . uniqid('Mocka');
        $parts = explode('\\', $this->_className);
        $this->_name = array_pop($parts);
        $this->_namespace = join('\\', $parts);

        $this->_parentClassName = (string) $className;
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

    public function load() {
        $code = $this->generateCode();
        eval($code);
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
            $method->extractFromClosure(function() {
                return $this->_callMethod(__METHOD__, func_get_args());
            });
            $class->addMethod($method);
        };
        return $class->dump();
    }

}
