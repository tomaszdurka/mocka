<?php

namespace Mocka\Invokable;

use Mocka\Exception;
use Mocka\Invocation;
use ReflectionFunctionAbstract;

class Spy extends AbstractInvokable {

    /** @var \ReflectionFunctionAbstract */
    private $_method;

    /**
     * @param ReflectionFunctionAbstract $method
     */
    public function __construct(ReflectionFunctionAbstract $method) {
        $this->_method = $method;
        parent::__construct();
    }

    /**
     * @param Invocation $invocation
     * @throws Exception
     */
    protected function _invoke(Invocation $invocation) {
        if ($this->_method instanceof \ReflectionMethod) {
            $this->_method->setAccessible(true);
            $result = $this->_method->invokeArgs($invocation->getContext(), $invocation->getArguments());
        } elseif ($this->_method instanceof \ReflectionFunction) {
            $result = $this->_method->invokeArgs($invocation->getArguments());
        } else {
            throw new Exception('Fatal error, method is not a ReflectionFunctionAbstract');
        }
        $invocation->setReturnValue($result);
    }
}
