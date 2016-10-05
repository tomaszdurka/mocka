<?php

namespace Mocka\Overrides\MethodOverrides;

use Mocka\Overrides\Context\AbstractContext;
use Mocka\Overrides\Context\ClassMethod;
use Mocka\Overrides\Context\StaticMethod;
use Mocka\Overrides\Manager;
use Mocka\Overrides\Override;
use Mocka\OverridableInterface;

class ClassOverrides extends AbstractOverrides {

    /** @var string */
    private $_className;

    /**
     * @param Manager $manager
     * @param string  $className
     */
    public function __construct(Manager $manager, $className) {
        $this->_className = $className;
        parent::__construct($manager);
    }

    public function getParentClassOverrides() {
        $class = new \ReflectionClass($this->_className);
        $parentClass = $class->getParentClass();
        if ($parentClass && $parentClass->implementsInterface(OverridableInterface::class)) {
            return new ClassOverrides($this->_manager, $parentClass->getName());
        }
        return null;
    }

    /**
     * @param string $methodName
     * @return Override|null
     */
    public function find($methodName) {
        $context = $this->_createContext($methodName);
        $override = $this->_manager->findByContext($context);

        if ($override) {
            return $override;
        }

        $parentClassOverrides = $this->getParentClassOverrides();
        if ($parentClassOverrides) {
            return $parentClassOverrides->find($methodName);
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
