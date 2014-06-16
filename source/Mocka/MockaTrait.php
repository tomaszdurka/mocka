<?php

namespace Mocka;

trait MockaTrait {

    /**
     * @param string $className
     * @return \Mocka\ClassMock
     */
    public function mockClass($className) {
        return new ClassMock($className);
    }

    /**
     * @param string     $className
     * @param array|null $constructorArgs
     * @return \Mocka\ClassTrait
     */
    public function mockObject($className, array $constructorArgs = null) {
        return $this->mockClass($className)->newInstance($constructorArgs);
    }
}
