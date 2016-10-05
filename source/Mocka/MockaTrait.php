<?php

namespace Mocka;

trait MockaTrait {

    /**
     * @param string|null $parentClassName
     * @param array|null  $interfaces
     * @param array|null  $traits
     * @return ClassMock
     */
    public function mockClass($parentClassName = null, array $interfaces = null, array $traits = null) {
        $factory = new ClassMockFactory();
        return $factory->loadClassMock(null, $parentClassName, $interfaces, $traits);
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
     * @param string $traitName
     * @return ClassMock
     */
    public function mockTrait($traitName) {
        $traitName = (string) $traitName;
        return $this->mockClass(null, null, [$traitName]);
    }

    /**
     * @param string     $parentClassName
     * @param array|null $constructorArgs
     * @return \Mocka\ClassMockTrait
     */
    public function mockObject($parentClassName = null, array $constructorArgs = null) {
        return $this->mockClass($parentClassName)->newInstance($constructorArgs);
    }
}
