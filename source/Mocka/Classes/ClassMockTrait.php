<?php

namespace Mocka\Classes;

use Mocka\Exception;
use Mocka\Invokables\Invokable\Spy;
use Mocka\Invokables\Invokable\Stub;
use Mocka\Overrides\MethodOverrides\ClassOverrides;
use Mocka\Overrides\MethodOverrides\InstanceOverrides;
use Mocka\Overrides\Manager;

trait ClassMockTrait {

    /**
     * @return InstanceOverrides
     */
    public function getOverrides() {
        $manager = Manager::getInstance();
        return new InstanceOverrides($manager, $this);
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

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     * @throws Exception
     */
    private function _callMethod($name, array $arguments) {
        foreach ($arguments as $i => $argument) {
            $arguments[$i] = &$argument;
        }
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
        foreach ($arguments as $i => $argument) {
            $arguments[$i] = &$argument;
        }
        $classDefinition = new ClassDefinition(get_called_class());
        $originalMethod = $classDefinition->findOriginalMethod($name);

        $manager = Manager::getInstance();
        $classOverrides = new ClassOverrides($manager, get_called_class());

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
