<?php

namespace Mocka\Classes;

use Mocka\Exception;
use Mocka\Invokables\Invokable\Spy;
use Mocka\Invokables\Invokable\Stub;
use Mocka\Overrides\MethodOverrides\ClassOverrides;
use Mocka\Overrides\MethodOverrides\InstanceOverrides;

trait ClassMockTrait {
    
    /** @var InstanceOverrides */
    private $_overrides;
    
    /** @var ClassOverrides */
    public static $_classOverrides;

    /**
     * @return InstanceOverrides
     */
    public function getOverrides() {
        if (null === $this->_overrides) {
            $this->_overrides = new InstanceOverrides($this);
        }
        return $this->_overrides;
    }

    /**
     * @return ClassOverrides
     */
    public static function getClassOverrides() {
        return self::$_classOverrides;
    }

    /**
     * @param ClassOverrides $classOverrides
     */
    public static function setClassOverrides(ClassOverrides $classOverrides) {
        self::$_classOverrides = $classOverrides;
    }

    /**
     * @param string $name
     * @return Stub
     * @deprecated
     */
    public function mockMethod($name) {
        return $this->getOverrides()->stub($name);
    }

    /**
     * @param string $name
     * @deprecated
     */
    public function unmockMethod($name) {
        $this->getOverrides()->remove($name);
    }

    public function __clone() {
        $this->_overrides = clone $this->_overrides;
    }

    /**
     * @param string $methodName
     * @return Stub
     */
    public function stub($methodName) {
        return $this->getOverrides()->stub($methodName);
    }

    /**
     * @param string $methodName
     * @return Spy
     */
    public function spy($methodName) {
        return $this->getOverrides()->spy($methodName);
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     * @throws Exception
     */
    private function _callMethod($name, array $arguments) {
        $classDefinition = new ClassDefinition(__CLASS__);
        $originalMethod = $classDefinition->findOriginalMethod($name);

        $override = $this->getOverrides()->find($name);

        if ($override) {
            $invokable = $override->getInvokable();
            if ($invokable instanceof Spy) {
                $returnValue = null;
                if ($originalMethod) {
                    $returnValue = call_user_func_array($originalMethod, $arguments);
                }
                $invokable->addInvocation($this, $arguments, $returnValue);
                return $returnValue;
            }
            if ($invokable instanceof Stub) {
                return $invokable->invoke($this, $arguments);
            }
            throw new Exception('Unsupported invokable');
        }
        if ($originalMethod) {
            if ('__construct' === $name) {
            }
            return call_user_func_array($originalMethod, $arguments);
        }

        if ('__construct' === $name) {
            return null;
        }
        throw new Exception("Cannot find method {$name}");
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     * @throws Exception
     */
    private static function _callStaticMethod($name, array $arguments) {
        $classDefinition = new ClassDefinition(get_called_class());
        $originalMethod = $classDefinition->findOriginalMethod($name);
        
        $classOverrides = self::getClassOverrides();

        $override = $classOverrides->find($name);
        if ($override) {
            $invokable = $override->getInvokable();
            if ($invokable instanceof Spy) {
                $returnValue = null;
                if ($originalMethod) {
                    $returnValue = call_user_func_array($originalMethod, $arguments);
                }
                $invokable->addInvocation(null, $arguments, $returnValue);
                return $returnValue;
            }
            if ($invokable instanceof Stub) {
                return $invokable->invoke(null, $arguments);
            }
            throw new Exception('Unsupported invokable');
        }
        if ($originalMethod) {
            return call_user_func_array($originalMethod, $arguments);
        }
        throw new Exception('Cannot find method');
    }
}
