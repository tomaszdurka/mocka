<?php

namespace Mocka\Overrides\MethodOverrides;

use Mocka\Overrides\Manager;
use Mocka\Overrides\Context\AbstractContext;
use Mocka\Overrides\Context\InstanceMethod;
use Mocka\Overrides\Override;

class InstanceOverrides extends AbstractOverrides {
    
    /** @var mixed */
    private $_instance;

    /**
     * @param Manager $manager
     * @param mixed   $instance
     */
    public function __construct(Manager $manager, $instance) {
        $this->_instance = $instance;
        parent::__construct($manager);
    }

    /**
     * @param string $methodName
     * @return Override|null
     */
    public function find($methodName) {
        $context = $this->_createContext($methodName);
        $override = $this->_manager->findByContext($context);
        if (!$override) {
            $override = $this->_getClassOverrides()->find($methodName);
        }
        return $override;
    }

    /**
     * @return ClassOverrides
     */
    protected function _getClassOverrides() {
        return new ClassOverrides($this->_manager, get_class($this->_instance));
    }

    /**
     * @param string $name
     * @return AbstractContext
     */
    protected function _createContext($name) {
        return new InstanceMethod($this->_instance, $name);
    }
}
