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

    protected function _invoke(Invocation $invocation) {
        $arguments = $invocation->getArguments();
        $closure = $this->_getClosure($this->getInvocations()->getCount());
        $result = call_user_func_array($closure, $arguments);
        $invocation->setReturnValue($result);
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
