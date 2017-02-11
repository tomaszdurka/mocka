<?php

namespace Mocka\Overrides\MethodOverrides;

use Mocka\Classes\ClassDefinition;
use Mocka\Exception;
use Mocka\Invokables\Invokable\Spy;
use Mocka\Invokables\Invokable\Stub;
use Mocka\Overrides\Context\AbstractContext;
use Mocka\Overrides\Override;

abstract class AbstractOverrides {

    /** @var Override[] */
    protected $_overrides;

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

    public function __construct() {
        $this->_overrides = [];
    }

    public function __clone() {
        foreach ($this->_overrides as $key => $override) {
            $this->_overrides[$key] = clone $override;
        }
    }

    /**
     * @return Override[]
     */
    public function findAll() {
        return $this->_overrides;
    }

    /**
     * @param string $methodName
     * @return \Mocka\Invokables\Invokable\AbstractInvokable
     * @throws Exception
     */
    public function get($methodName) {
        $override = $this->find($methodName);
        if (!$override) {
            throw new Exception('Override not found');
        }
        return $override->getInvokable();
    }

    /**
     * @param string $methodName
     */
    public function remove($methodName) {
        $this->_overrides = \Functional\reject($this->_overrides, function (Override $override) use ($methodName) {
            return $override->getContext()->getMethodName() === $methodName;
        });
    }

    /**
     * @param string $methodName
     * @return Stub
     */
    public function stub($methodName) {
        $context = $this->_createContext($methodName);
        $invokable = new Stub();
        $override = new Override($context, $invokable);
        $this->_add($override);
        return $invokable;
    }

    /**
     * @param string $methodName
     * @return Spy
     */
    public function spy($methodName) {
        $context = $this->_createContext($methodName);
        $invokable = new Spy();
        $override = new Override($context, $invokable);
        $this->_add($override);
        return $invokable;
    }

    /**
     * @param Override $override
     */
    protected function _add(Override $override) {
        $this->remove($override->getContext()->getMethodName());
        $this->_overrides[] = $override;
    }

    /**
     * @param string $methodName
     * @return Override|null
     */
    protected function _find($methodName) {
        return \Functional\first($this->_overrides, function (Override $override) use ($methodName) {
            return $override->getContext()->getMethodName() === $methodName;
        });
    }
}
