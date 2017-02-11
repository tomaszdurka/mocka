<?php

namespace Mocka\Overrides\MethodOverrides;

use Mocka\Overrides\Context\AbstractContext;
use Mocka\Overrides\Context\ClassMethod;
use Mocka\Overrides\Override;
use Mocka\Overrides\OverridableInterface;

class ClassOverrides extends AbstractOverrides {

    /** @var string */
    private $_className;

    /**
     * @param string  $className
     */
    public function __construct($className) {
        $this->_className = $className;
        parent::__construct();
    }

    /**
     * @param string $methodName
     * @return Override|null
     */
    public function find($methodName) {
        $override = $this->_find($methodName);
        if ($override) {
            return $override;
        }
        
        $parentClassOverrides = $this->_getParentClassOverrides();
        if ($parentClassOverrides) {
            return $parentClassOverrides->find($methodName);
        }
        return null;
    }

    /**
     * @return ClassOverrides|null
     */
    protected function _getParentClassOverrides() {
        $class = new \ReflectionClass($this->_className);
        $parentClass = $class->getParentClass();
        if ($parentClass && $parentClass->implementsInterface(OverridableInterface::class)) {
            return $parentClass->getMethod('getClassOverrides')->invoke(null);
        }
        return null;
    }

    /**
     * @param string $name
     * @return AbstractContext
     */
    protected function _createContext($name) {
        return new ClassMethod($this->_className, $name);
    }
}
