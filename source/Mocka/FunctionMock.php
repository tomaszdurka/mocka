<?php

namespace Mocka;

class FunctionMock {

    /** @var \Closure */
    private $_defaultClosure;

    /** @var \Closure[] */
    private $_orderedClosures;

    /** @var Invocations */
    private $_invocations;

    public function __construct() {
        $this->_orderedClosures = array();
        $this->_defaultClosure = function () {
        };
        $this->_invocations = new Invocations();
    }

    /**
     * @param mixed|\Closure $body
     * @return static
     */
    public function set($body) {
        $this->_defaultClosure = $this->_normalizeBody($body);
        return $this;
    }

    /**
     * @param int|int[] $at
     * @param string|\Closure $body
     * @return static
     */
    public function at($at, $body) {
        $ats = (array) $at;
        foreach ($ats as $at) {
            $at = (int) $at;
            $closure = $this->_normalizeBody($body);
            $this->_orderedClosures[$at] = $closure;
        }
        return $this;
    }

    /**
     * @param array|null $arguments
     * @return mixed
     */
    public function invoke(array $arguments = null) {
        $arguments = (array) $arguments;
        $closure = $this->_getClosure($this->getInvocations()->getCount());

        $invocation = new Invocation($arguments);
        $this->getInvocations()->add($invocation);
        $result = call_user_func_array($closure, $arguments);

        $invocation->setReturnValue($result);
        return $result;
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
     * @return Invocation
     */
    public function getLastCall() {
        return $this->getInvocations()->getLast();
    }

    /**
     * @return int
     */
    public function getCallCount() {
        return $this->getInvocations()->getCount();
    }

    public function __invoke() {
        return $this->invoke(func_get_args());
    }

    /**
     * @param int $at
     * @return \Closure
     */
    private function _getClosure($at) {
        $at = (int) $at;
        if (array_key_exists($at, $this->_orderedClosures)) {
            return $this->_orderedClosures[$at];
        }
        return $this->_defaultClosure;
    }

    /**
     * @param mixed|\Closure $body
     * @return \Closure
     */
    private function _normalizeBody($body) {
        $closure = $body;
        if (!$closure instanceof \Closure) {
            $closure = function () use ($body) {
                return $body;
            };
        }
        return $closure;
    }
}
