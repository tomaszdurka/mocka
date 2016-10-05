<?php

namespace Mocka\Classes;

use Mocka\Invokable\AbstractInvokable;
use Mocka\Invokable\Spy;
use Mocka\Invokable\Stub;

class ClassDefinition {

    /** @var string */
    private $_className;

    /**
     * @param string $className
     */
    public function __construct($className) {
        $this->_className = (string) $className;
    }

    /**
     * @param string $methodName
     * @return AbstractInvokable|null
     */
    public function findOriginalMethod($methodName) {
        $method = $this->_findOriginalMethodReflection($methodName);
        if ($method) {
            if ($method->isAbstract()) {
                return new Stub();
            } else {
                return new Spy($method);
            }
        }
        return null;
    }

    /**
     * @param string $methodName
     * @return \ReflectionMethod|null
     */
    private function _findOriginalMethodReflection($methodName) {
        $class = new \ReflectionClass($this->_className);

        $traitAliasName = '_mockaTraitAlias_' . $methodName;
        if ($class->hasMethod($traitAliasName)) {
            return $class->getMethod($traitAliasName);
        }

        if ($class->getParentClass() && $class->getParentClass()->hasMethod($methodName)) {
            return $class->getParentClass()->getMethod($methodName);
        }

        return null;
    }
}
