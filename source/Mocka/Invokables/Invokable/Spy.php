<?php

namespace Mocka\Invokables\Invokable;

use Mocka\Exception;
use Mocka\Invokables\Invocation;
use ReflectionFunctionAbstract;

class Spy extends AbstractInvokable {

    /**
     * @param mixed $context
     * @param array $arguments
     * @param mixed $returnValue
     */
    public function addInvocation($context, $arguments, $returnValue) {
        $invocation = new Invocation($context, $arguments);
        $invocation->setReturnValue($returnValue);
        $this->getInvocations()->add($invocation);
    }
}
