<?php

namespace Mocka\Overrides;

use Mocka\Invokables\Invokable\AbstractInvokable;
use Mocka\Overrides\Context\AbstractContext;

class Override {

    /** @var AbstractContext */
    private $_context;

    /** @var AbstractInvokable */
    private $_invokable;

    /**
     * @param AbstractContext   $context
     * @param AbstractInvokable $invokable
     */
    public function __construct(AbstractContext $context, AbstractInvokable $invokable) {
        $this->_context = $context;
        $this->_invokable = $invokable;
    }

    /**
     * @return AbstractContext
     */
    public function getContext() {
        return $this->_context;
    }
    
    /**
     * @return AbstractInvokable
     */
    public function getInvokable() {
        return $this->_invokable;
    }
}
