<?php

namespace Mocka\Classes;

use CodeGenerator\ClassBlock;
use CodeGenerator\MethodBlock;
use CodeGenerator\TraitBlock;
use Mocka\Overrides\OverridableInterface;

class ClassWrapper {

    /** @var string */
    private $_className;

    /** @var string|null */
    private $_parentClassName;

    /** @var array */
    private $_interfaces;

    /** @var array */
    private $_traits;

    /**
     * @param string|null $parentClassName
     * @param array       $interfaces
     * @param array       $traits
     */
    public function __construct($parentClassName, array $interfaces, array $traits) {
        $this->_className = 'MockaClassWrapper' . uniqid();
        if (null !== $parentClassName) {
            $this->_parentClassName = (string) $parentClassName;
        }
        sort($interfaces);
        $this->_interfaces = $interfaces;
        $this->_traits = $traits;
    }

    /**
     * @return string
     */
    public function generateCode() {
        $class = new ClassBlock($this->_className);
        if ($this->_parentClassName) {
            $class->setParentClassName($this->_parentClassName);
        }
        foreach ($this->_interfaces as $interface) {
            $class->addInterface($interface);
        }
        foreach ($this->_traits as $trait) {
            $reflectionTrait = new \ReflectionClass($trait);
            $trait = new TraitBlock($trait);
            foreach ($reflectionTrait->getMethods() as $reflectionMethod) {
                if (!$reflectionMethod->isAbstract()) {
                    $trait->addAlias($reflectionMethod->getName(), "_mockaTraitAlias_{$reflectionMethod->getName()}");
                }
            }
            $class->addUse($trait);
        }

        $skipTrait = false;
        if ($this->_parentClassName) {
            $parentClass = new \ReflectionClass($this->_parentClassName);
            $skipTrait = $parentClass->implementsInterface(OverridableInterface::class);
        }

        if (!$skipTrait) {
            $class->addUse(new TraitBlock(OverridableTrait::class));
            if ($this->_parentClassName) {
                $reflection = new \ReflectionClass($this->_parentClassName);
                if (!$reflection->hasMethod('__clone') || !$reflection->getMethod('__clone')->isFinal()) {
                    $class->addUse(new TraitBlock(OverridableCloneableTrait::class));
                }
            }
        }

        $mockableMethods = $this->_getMockableMethods();
        foreach ($mockableMethods as $reflectionMethod) {
            $method = new MethodBlock($reflectionMethod->getName());
            $method->setAbstract(false);
            $method->setParametersFromReflection($reflectionMethod);
            $method->setStaticFromReflection($reflectionMethod);
            $method->setVisibilityFromReflection($reflectionMethod);
            $method->setReturnTypeFromReflection($reflectionMethod);

            if ($reflectionMethod->isStatic()) {
                if ($this->_isVoidReturnType($reflectionMethod)) {
                    $method->extractFromClosure(function () {
                        static::_callStaticMethod(__FUNCTION__, func_get_args());
                    });
                } else {
                    $method->extractFromClosure(function () {
                        return static::_callStaticMethod(__FUNCTION__, func_get_args());
                    });
                }
            } else {
                if ($this->_isVoidReturnType($reflectionMethod)) {
                    $method->extractFromClosure(function () {
                        $this->_callMethod(__FUNCTION__, func_get_args());
                    });
                } else {
                    $method->extractFromClosure(function () {
                        return $this->_callMethod(__FUNCTION__, func_get_args());
                    });
                }
            }
            $class->addMethod($method);
        }

        $method = new MethodBlock('__construct');
        $method->extractFromClosure(function () {
            return $this->_callMethod(__FUNCTION__, func_get_args());
        });
        $class->addMethod($method);
        return $class->dump();
    }

    /**
     * @return string
     */
    public function getClassName() {
        return $this->_className;
    }

    public function load() {
        $code = $this->generateCode();
        eval($code);
    }

    /**
     * @return string[]
     */
    protected function _getReservedKeywords() {
        return ['__halt_compiler', 'abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch', 'class', 'clone', 'const', 'continue',
            'declare', 'default', 'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach', 'endif', 'endswitch',
            'endwhile', 'eval', 'exit', 'extends', 'final', 'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include',
            'include_once', 'instanceof', 'insteadof', 'interface', 'isset', 'list', 'namespace', 'new', 'or', 'print', 'private', 'protected',
            'public', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'unset', 'use', 'var', 'while', 'xor'];
    }

    /**
     * @return \ReflectionMethod[]
     */
    protected function _getMockableMethods() {
        /** @var \ReflectionMethod[] $methods */
        $methods = [];
        $interfaces = array_merge($this->_interfaces, $this->_traits);
        if ($this->_parentClassName) {
            $interfaces[] = $this->_parentClassName;
        }
        foreach ($interfaces as $interface) {
            $reflectionClass = new \ReflectionClass($interface);
            foreach ($reflectionClass->getMethods() as $method) {
                $methods[$method->getName()] = $method;
            }
        }

        $reflectionTrait = new \ReflectionClass(OverridableTrait::class);
        $methods = array_filter($methods, function (\ReflectionMethod $reflectionMethod) use ($reflectionTrait) {
            if ($reflectionMethod->isConstructor()) {
                return false;
            }
            if ($reflectionMethod->isPrivate() || $reflectionMethod->isFinal()) {
                return false;
            }
            if ($reflectionTrait->hasMethod($reflectionMethod->getName())) {
                return false;
            }
            if (in_array($reflectionMethod->getName(), $this->_getReservedKeywords())) {
                return false;
            }
            return true;
        });
        return $methods;
    }

    /**
     * @param \ReflectionMethod $method
     * @return bool
     */
    protected function _isVoidReturnType(\ReflectionMethod $method): bool {
        if (!$method->hasReturnType()) {
            return false;
        }
        $returnType = $method->getReturnType();
        if (!($returnType instanceof \ReflectionNamedType)) {
            return false;
        }
        return $returnType->getName() === 'void';
    }
}
