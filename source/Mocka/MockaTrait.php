<?php

namespace Mocka;

trait MockaTrait {

    /**
     * @param string     $className
     * @param array|null $interfaces
     * @return \Mocka\ClassMock
     */
    public function mockClass($className, array $interfaces = null) {
        return new ClassMock($className, $interfaces);
    }

    /**
     * @param string $interfaceName
     * @return ClassMock
     */
    public function mockInterface($interfaceName) {
        $interfaceName = (string) $interfaceName;
        return new ClassMock(null, [$interfaceName]);
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
