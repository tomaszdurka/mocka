<?php

namespace MockaTests\Mocka;

use Mocka\ClassMock;

class ClassMockTest extends \PHPUnit_Framework_TestCase {

    public function testGenerateCode() {
        $parentClassName = '\\MockaMocks\\AbstractClass';

        $classMock = new ClassMock($parentClassName);
        $name = $classMock->getName();
        $namespace = $classMock->getNamespace();
        $expectedMockCode = <<<EOD
namespace $namespace;

class $name extends $parentClassName {

    use \\Mocka\\ClassTrait;

    public function foo() {
        return \$this->_callMethod(__METHOD__, func_get_args());
    }

    public function bar() {
        return \$this->_callMethod(__METHOD__, func_get_args());
    }
}
EOD;
        $this->assertSame($expectedMockCode, $classMock->generateCode());
    }

}
