<?php

namespace Mocka\Classes;

class ClassMockFactory {

    /** @var ClassWrapper[] */
    private $_wrappers;

    public function __construct() {
        $this->_wrappers = [];
    }

    /**
     * @param string|null $className
     * @param string|null $parentClassName
     * @param array|null  $interfaces
     * @param array|null  $traits
     * @return ClassMock
     */
    public function loadClassMock($className = null, $parentClassName = null, array $interfaces = null, array $traits = null) {
        $classWrapper = $this->_loadClassWrapper($parentClassName, (array) $interfaces, (array) $traits);
        $classMock = new ClassMock($classWrapper->getClassName(), $className);
        $classMock->load();
        return $classMock;
    }

    /**
     * @param string|null $parentClassName
     * @param array       $interfaces
     * @param array       $traits
     * @return ClassWrapper
     */
    protected function _loadClassWrapper($parentClassName, array $interfaces, array $traits) {
        $hash = $this->_getHash($parentClassName, $interfaces, $traits);
        if (!array_key_exists($hash, $this->_wrappers)) {
            $classWrapper = new ClassWrapper($parentClassName, $interfaces, $traits);
            $this->_wrappers[$hash] = $classWrapper;
            $classWrapper->load();
        }
        return $this->_wrappers[$hash];
    }

    /**
     * @param string|null $parentClassName
     * @param array       $interfaces
     * @param array       $traits
     * @return string
     */
    protected function _getHash($parentClassName, array $interfaces, array $traits) {
        sort($interfaces);
        $mergedInterfaces = array_merge([$parentClassName], $interfaces, $traits);
        $mergedInterfaces = array_filter($mergedInterfaces);
        return join(',', $mergedInterfaces);
    }
}
