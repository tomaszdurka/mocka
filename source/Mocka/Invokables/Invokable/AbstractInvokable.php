<?php

namespace Mocka\Invokables\Invokable;

use Mocka\Invokables\Invocation;
use Mocka\Invokables\Invocations;

abstract class AbstractInvokable {

    /** @var Invocations */
    private $_invocations;

    /**
     * @param Invocation $invocation
     */
    abstract protected function _invoke(Invocation $invocation);

    public function __construct() {
        $this->_invocations = new Invocations();
    }

    /**
     * @param mixed|null $context
     * @param array      $arguments
     * @return mixed|null
     */
    public function invoke($context, array $arguments) {
        $invocation = new Invocation($context, $arguments);
        $this->_invoke($invocation);
        $this->getInvocations()->add($invocation);
        return $invocation->getReturnValue();
    }

    /**
     * @return Invocations
     */
    public function getInvocations() {
        return $this->_invocations;
    }

    /**
     * @return Invocations
     */
    public function getCalls() {
        return $this->getInvocations();
    }

    /**
     * @param int $number
     * @return Invocation
     */
    public function getCall($number) {
        return $this->getCalls()->get($number);
    }

    /**
     * @return int
     */
    public function getCallCount() {
        return $this->getInvocations()->getCount();
    }
}
