<?php

namespace Mocka\Overrides\MethodOverrides;

use Mocka\Exception;
use Mocka\Invokable\Stub;
use Mocka\Overrides\Context\AbstractContext;
use Mocka\Overrides\Manager;
use Mocka\Overrides\Override;

abstract class AbstractOverrides {

    /** @var Manager */
    protected $_manager;

    /**
     * @param $methodName
     * @return Override|null
     */
    abstract public function find($methodName);

    /**
     * @param string $name
     * @return AbstractContext
     */
    abstract protected function _createContext($name);

    /**
     * @param Manager $manager
     */
    public function __construct(Manager $manager) {
        $this->_manager = $manager;
    }

    /**
     * @param string $methodName
     * @return Override
     * @throws Exception
     */
    public function get($methodName) {
        $override = $this->find($methodName);
        if (!$override) {
            throw new Exception('Override not found');
        }
        return $override;
    }

    /**
     * @param string $methodName
     */
    public function remove($methodName) {
        $context = $this->_createContext($methodName);
        $this->_manager->removeByContext($context);
    }

    /**
     * @param string $methodName
     * @return Stub
     */
    public function stub($methodName) {
        $context = $this->_createContext($methodName);
        $this->_manager->removeByContext($context);
        $invokable = new Stub();
        $override = new Override($context, $invokable);
        $this->_manager->add($override);
        return $invokable;
    }
}
