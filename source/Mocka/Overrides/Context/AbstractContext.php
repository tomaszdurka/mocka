<?php

namespace Mocka\Overrides\Context;

abstract class AbstractContext {

    /**
     * @param AbstractContext $context
     * @return bool
     */
    abstract public function equals(AbstractContext $context);

    /**
     * @return string
     */
    abstract public function getClassName();

    /**
     * @return string
     */
    abstract public function getMethodName();

}
