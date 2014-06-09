<?php

namespace Mocka;

class ClassMock {

    /** @var string */
    private $_className;

    /** @var string */
    private $_parentClassName;

    public function __construct($className) {
        $this->_className = $className . uniqid('Mocka');
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
    public function generateCode() {
        $class = new \CG_Class($this->_className, $this->_parentClassName);

        $class->addUse('Mocka\ClassTrait');

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
