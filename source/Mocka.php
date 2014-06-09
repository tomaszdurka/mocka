<?php

class Mocka {

    /**
     * @param string $className
     * @return string
     */
    public function mockClass($className) {
        $mockedClass = new \Mocka\ClassMock($className);
        $mockedClassName = $mockedClass->getClassName();
        $mockedClass->load();
        return $mockedClassName;
    }

    /**
     * @param string     $className
     * @param array|null $constructorArgs
     * @return object
     */
    public function mockObject($className, array $constructorArgs = null) {
        $constructorArgs = (array) $constructorArgs;
        $mockedClassReflection = new ReflectionClass($this->mockClass($className));
        return $mockedClassReflection->newInstanceArgs($constructorArgs);
    }
}
