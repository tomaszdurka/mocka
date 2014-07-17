<?php

namespace Mocka;

trait MockaTrait {

    /**
     * @param string     $parentClassName
     * @param array|null $interfaces
     * @return \Mocka\ClassMock
     */
    public function mockClass($parentClassName, array $interfaces = null) {
        return new ClassMock(null, $parentClassName, $interfaces);
    }

    /**
     * @param string $interfaceName
     * @return ClassMock
     */
    public function mockInterface($interfaceName) {
        $interfaceName = (string) $interfaceName;
        return $this->mockClass(null, [$interfaceName]);
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
