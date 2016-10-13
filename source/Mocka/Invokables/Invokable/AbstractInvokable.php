<?php

namespace Mocka\Invokables\Invokable;

use Mocka\Invokables\Invocation;
use Mocka\Invokables\Invocations;

abstract class AbstractInvokable {

    /** @var Invocations */
    private $_invocations;

    public function __construct() {
        $this->_invocations = new Invocations();
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
