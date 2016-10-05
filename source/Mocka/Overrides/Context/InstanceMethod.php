<?php

namespace Mocka\Overrides\Context;

class InstanceMethod extends AbstractContext {

    /** @var mixed */
    private $_instance;

    /** @var string */
    private $_methodName;

    /**
     * @param mixed  $object
     * @param string $methodName
     */
    public function __construct($object, $methodName) {
        $this->_instance = $object;
        $this->_methodName = (string) $methodName;
    }

    /**
     * @return mixed
     */
    public function getInstance() {
        return $this->_instance;
    }

    /**
     * @return string
     */
    public function getMethodName() {
        return $this->_methodName;
    }

    public function equals(AbstractContext $context) {
        return $context instanceof InstanceMethod
        && $this->getInstance() === $context->getInstance()
        && $this->getMethodName() === $context->getMethodName();
    }
}
