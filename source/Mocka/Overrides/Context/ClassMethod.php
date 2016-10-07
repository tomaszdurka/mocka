<?php

namespace Mocka\Overrides\Context;

class ClassMethod extends AbstractContext {

    /** @var string */
    private $_className;

    /** @var string */
    private $_methodName;

    /**
     * @param string $className
     * @param string $methodName
     */
    public function __construct($className, $methodName) {
        $this->_className = (string) $className;
        $this->_methodName = (string) $methodName;
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
    public function getMethodName() {
        return $this->_methodName;
    }

    public function equals(AbstractContext $context) {
        return $context instanceof ClassMethod
        && $this->getClassName() === $context->getClassName()
        && $this->getMethodName() === $context->getMethodName();
    }

}
