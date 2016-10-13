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
     * @return Callable|null
     */
    public function findOriginalMethod($methodName) {
        $class = new \ReflectionClass($this->_className);

        $traitAliasName = '_mockaTraitAlias_' . $methodName;
        if ($class->hasMethod($traitAliasName)) {
            if ($class->getMethod($traitAliasName)->isAbstract()) {
                return function () {};
            }
            return ['self', $traitAliasName];
        }

        $parentClass = $this->_findOriginalParentClass($class);
        if ($parentClass && $parentClass->hasMethod($methodName)) {
            if ($parentClass->getMethod($methodName)->isAbstract()) {
                return function () {};
            }
            return ['parent', $methodName];
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
