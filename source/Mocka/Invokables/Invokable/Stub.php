<?php

namespace Mocka\Invokables\Invokable;

use Mocka\Invokables\Invocation;

class Stub extends AbstractInvokable {

    /** @var \Closure */
    private $_defaultClosure;

    /** @var \Closure[] */
    private $_orderedClosures;

    public function __construct() {
        $this->_orderedClosures = [];
        $this->_defaultClosure = function () {
        };
        parent::__construct();
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
     * @param int|int[]       $at
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
     * @param mixed|null $context
     * @param array      $arguments
     * @return mixed|null
     */
    public function invoke($context, array $arguments) {
        foreach ($arguments as $i => &$argument) {
            $arguments[$i] = &$argument;
        }
        $invocation = new Invocation($context, $arguments);
        $closure = $this->_getClosure($this->getInvocations()->getCount());
        $result = call_user_func_array($closure, $arguments);
        $invocation->setReturnValue($result);
        $this->getInvocations()->add($invocation);
        return $result;
    }

    /**
     * @param int $at
     * @return \Closure
     */
    protected function _getClosure($at) {
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
    protected function _normalizeBody($body) {
        $closure = $body;
        if (!$closure instanceof \Closure) {
            $closure = function () use ($body) {
                return $body;
            };
        }
        return $closure;
    }
}
