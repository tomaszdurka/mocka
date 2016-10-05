<?php

namespace Mocka\Classes;

use Mocka\Exception;
use Mocka\Invokable\AbstractInvokable;
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
     * @return AbstractInvokable
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
     * @param string     $name
     * @param array      $arguments
     * @return mixed
     * @throws Exception
     */
    private function _callMethod($name, array $arguments) {
        $override = $this->getOverrides()->find($name);
        if ($override) {
            return $override->getInvokable()->invoke($this, $arguments);
        }
        $classDefinition = new ClassDefinition(__CLASS__);
        $originalMethod = $classDefinition->findOriginalMethod($name);
        if ($originalMethod) {
            return $originalMethod->invoke($this, $arguments);
        }
        if ('__construct' === $name) {
            return null;
        }

        throw new Exception('Cannot find method');
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments) {
        return static::_callStaticMethod($name, $arguments);
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     * @throws Exception
     */
    private static function _callStaticMethod($name, array $arguments) {
        $manager = Manager::getInstance();
        $classOverrides = new ClassOverrides($manager, get_called_class());
        $override = $classOverrides->find($name);
        if ($override) {
            return $override->getInvokable()->invoke(null, $arguments);
        }

        $classDefinition = new ClassDefinition(get_called_class());
        $originalMethod = $classDefinition->findOriginalMethod($name);
        if ($originalMethod) {
            return $originalMethod->invoke(null, $arguments);
        }
        throw new Exception('Cannot find method');
    }
}
