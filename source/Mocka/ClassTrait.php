<?php

namespace Mocka;

trait ClassTrait {

    /** @var ClassMock */
    private static $_classMock;

    /**
     * @param ClassMock $classMock
     */
    public static function setMockClass(ClassMock $classMock) {
        static::$_classMock = $classMock;
    }

    /**
     * @return ClassMock
     */
    protected static function _getMockClass() {
        return static::$_classMock;
    }
}
