<?php

namespace MockaTests\Mocka;

use Mocka\Classes\ClassWrapper;

class ClassWrapperTest extends \PHPUnit_Framework_TestCase {

    public function testGenerateCode() {
        $parentClassName = '\\MockaMocks\\AbstractClass';

        $classWrapper = new ClassWrapper($parentClassName, [], []);
        $className = $classWrapper->getClassName();
        $expectedMockCode = <<<EOD
class $className extends $parentClassName implements \Mocka\Overrides\OverridableInterface {

    use \Mocka\Classes\ClassMockTrait;

    public function foo() {
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

    public function __construct() {
        return \$this->_callMethod(__FUNCTION__, func_get_args());
    }
}
EOD;
        $this->assertSame($expectedMockCode, $classWrapper->generateCode());
    }

    public function testGenerateCodeInterface() {
        $parentInterfaceName = '\\MockaMocks\\InterfaceMock';
        $classWrapper = new ClassWrapper(null, [$parentInterfaceName], []);
        $className = $classWrapper->getClassName();
        $expectedMockCode = <<<EOD
class $className implements $parentInterfaceName, \Mocka\Overrides\OverridableInterface {

    use \Mocka\Classes\ClassMockTrait;

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
        $this->assertSame($expectedMockCode, $classWrapper->generateCode());
    }

    public function testGenerateCodeTrait() {
        $traitName = '\\MockaMocks\\TraitMock';
        $classWrapper = new ClassWrapper(null, [], [$traitName]);
        $className = $classWrapper->getClassName();
        $expectedMockCode = <<<EOD
class $className implements \Mocka\Overrides\OverridableInterface {

    use $traitName {
        traitMethod as _mockaTraitAlias_traitMethod;
        bar as _mockaTraitAlias_bar;
    }

    use \Mocka\Classes\ClassMockTrait;

    public function abstractTraitMethod() {
        return \$this->_callMethod(__FUNCTION__, func_get_args());
    }

    public function traitMethod() {
        return \$this->_callMethod(__FUNCTION__, func_get_args());
    }

    public function bar() {
        return \$this->_callMethod(__FUNCTION__, func_get_args());
    }

    public function __construct() {
        return \$this->_callMethod(__FUNCTION__, func_get_args());
    }
}
EOD;
        $this->assertSame($expectedMockCode, $classWrapper->generateCode());
    }
}
