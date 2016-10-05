<?php

namespace Mocka\Classes;

use Mocka\Invokables\Invokable\AbstractInvokable;
use Mocka\Invokables\Invokable\Spy;
use Mocka\Invokables\Invokable\Stub;
use Mocka\Overrides\OverridableInterface;

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
        $method = $this->findOriginalMethodReflection($methodName);
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
    public function findOriginalMethodReflection($methodName) {
        $class = new \ReflectionClass($this->_className);

        $traitAliasName = '_mockaTraitAlias_' . $methodName;
        if ($class->hasMethod($traitAliasName)) {
            return $class->getMethod($traitAliasName);
        }

        $parentClass = $this->_findOriginalParentClass($class);
        if ($parentClass && $parentClass->hasMethod($methodName)) {
            return $parentClass->getMethod($methodName);
        }

        return null;
    }

    /**
     * @param \ReflectionClass $class
     * @return \ReflectionClass|null
     */
    protected function _findOriginalParentClass(\ReflectionClass $class) {
        while ($class = $class->getParentClass()) {
            if (!$class->implementsInterface(OverridableInterface::class)) {
                return $class;
            }
        }
        return null;
    }
}
