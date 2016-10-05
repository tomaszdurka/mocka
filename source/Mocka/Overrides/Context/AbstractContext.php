<?php

namespace Mocka\Overrides\Context;

use Mocka\Invokable\AbstractInvokable;

abstract class AbstractContext {

    /**
     * @param AbstractContext $context
     * @return bool
     */
    abstract public function equals(AbstractContext $context);

}
