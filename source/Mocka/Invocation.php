<?php

namespace Mocka;

class Invocation {

    /** @var array */
    private $_arguments;

    /** @var mixed|null */
    private $_returnValue;

    /**
     * @param array $arguments
     */
    public function __construct(array $arguments) {
        $this->_arguments = $arguments;
    }

    /**
     * @param mixed|null $returnValue
     */
    public function setReturnValue($returnValue) {
        $this->_returnValue = $returnValue;
    }

    /**
     * @return mixed|null
     */
    public function getReturnValue() {
        return $this->_returnValue;
    }

    /**
     * @return array
     */
    public function getArguments() {
        return $this->_arguments;
    }

    /**
     * @param $number
     * @return mixed
     * @throws Exception
     */
    public function getArgument($number) {
        if (!array_key_exists($number, $this->_arguments)) {
            throw new Exception('Argument not found');
        }
        return $this->_arguments[$number];
    }
}
