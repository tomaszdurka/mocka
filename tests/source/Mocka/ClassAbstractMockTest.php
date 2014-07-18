<?php

namespace MockaTests\Mocka;

use Mocka\ClassAbstractMock;
use Mocka\AbstractClassTrait;

class ClassAbstractMockTest extends \PHPUnit_Framework_TestCase {

    public function testGenerateCode() {
        $parentClassName = '\\MockaMocks\\AbstractClass';

        $classMock = new ClassAbstractMock($parentClassName, []);
        $className = $this->_getClassName($classMock);
        $expectedMockCode = <<<EOD
class $className extends $parentClassName {

    use \Mocka\AbstractClassTrait;

    public function foo() {
        return \$this->_callMethod(__FUNCTION__, func_get_args());
    }

    public function __construct(\$arg1, \$arg2 = null) {
        return \$this->_callMethod(__FUNCTION__, func_get_args());
    }

    public function bar() {
        return \$this->_callMethod(__FUNCTION__, func_get_args());
    }

    protected function _foo() {
        return \$this->_callMethod(__FUNCTION__, func_get_args());
    }

    public static function jar() {
        return static::_callStaticMethod(__FUNCTION__, func_get_args());
    }

    protected static function _jar() {
        return static::_callStaticMethod(__FUNCTION__, func_get_args());
    }

    public function interfaceMethod() {
        return \$this->_callMethod(__FUNCTION__, func_get_args());
    }
}
EOD;
        $this->assertSame($expectedMockCode, $classMock->generateCode());
    }

    public function testGenerateCodeInterface() {
        $parentInterfaceName = '\\MockaMocks\\InterfaceMock';
        $classMock = new ClassAbstractMock(null, [$parentInterfaceName]);
        $className = $this->_getClassName($classMock);
        $expectedMockCode = <<<EOD
class $className implements $parentInterfaceName {

    use \Mocka\AbstractClassTrait;

    public function zoo() {
        return \$this->_callMethod(__FUNCTION__, func_get_args());
    }

    public function interfaceMethod() {
        return \$this->_callMethod(__FUNCTION__, func_get_args());
    }

    public function __construct() {
        return \$this->_callMethod(__FUNCTION__, func_get_args());
    }
}
EOD;
        $this->assertSame($expectedMockCode, $classMock->generateCode());
    }

    /**
     * @param ClassAbstractMock $classAbstractMock
     * @return string
     */
    private function _getClassName(ClassAbstractMock $classAbstractMock) {
        $getClassName = (new \ReflectionClass($classAbstractMock))->getMethod('_getClassName');
        $getClassName->setAccessible(true);
        return $getClassName->invoke($classAbstractMock);
    }
}
