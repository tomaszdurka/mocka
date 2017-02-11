<?php

namespace Mocka\Overrides\MethodOverrides;

use Mocka\Overrides\Context\AbstractContext;
use Mocka\Overrides\Context\InstanceMethod;
use Mocka\Overrides\Override;

class InstanceOverrides extends AbstractOverrides {

    /** @var mixed */
    private $_instance;

    /**
     * @param mixed          $instance
     */
    public function __construct($instance) {
        $this->_instance = $instance;
        parent::__construct();
    }

    /**
     * @param string $methodName
     * @return Override|null
     */
    public function find($methodName) {
        $override = $this->_find($methodName);
        if (!$override) {
            $override = $this->_getClassOverrides()->find($methodName);
        }
        return $override;
    }

    /**
     * @return ClassOverrides
     */
    protected function _getClassOverrides() {
//        $class = new \ReflectionClass($this->_instance);
//        return $class->getMethod('getClassOverrides')->invoke(null);
        
        $className = get_class($this->_instance);
        return $className::getClassOverrides();
    }

    /**
     * @param string $name
     * @return AbstractContext
     */
    protected function _createContext($name) {
        return new InstanceMethod($this->_instance, $name);
    }
}
