<?php

class Mocka {

    /**
     * @param string $className
     * @return \Mocka\ClassMock
     */
    public function mockClass($className) {
        return new \Mocka\ClassMock($className);
    }

    /**
     * @param string     $className
     * @param array|null $constructorArgs
     * @return object
     */
    public function mockObject($className, array $constructorArgs = null) {
        return $this->mockClass($className)->newInstance($constructorArgs);
    }
}
