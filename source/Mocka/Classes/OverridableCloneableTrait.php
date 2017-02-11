<?php

namespace Mocka\Classes;

trait OverridableCloneableTrait {
    
    public function __clone() {
        $this->_overrides = clone $this->_overrides;
    }
}
