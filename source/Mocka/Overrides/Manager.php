<?php

namespace Mocka\Overrides;

use Mocka\Overrides\Context\AbstractContext;
use Mocka\Overrides\Context\ClassMethod;
use Mocka\Overrides\Context\GlobalFunction;
use Mocka\Overrides\Context\InstanceMethod;
use Mocka\Overrides\Context\StaticMethod;

class Manager {

    /** @var self */
    private static $_instance;

    /** @var AbstractContext[] */
    public $_list;

    public function __construct() {
        $this->_list = [];
    }

    /**
     * @param Override $override
     */
    public function add(Override $override) {
        $this->_list[] = $override;
    }

    /**
     * @param AbstractContext $context
     * @return Override|null
     */
    public function findByContext(AbstractContext $context) {
        return \Functional\first($this->_list, function (Override $override) use ($context) {
            return $override->getContext()->equals($context);
        });
    }

    /**
     * @param AbstractContext $context
     */
    public function removeByContext(AbstractContext $context) {
        $this->_list = \Functional\reject($this->_list, function (Override $override) use ($context) {
            return $override->getContext()->equals($context);
        });
    }

    /**
     * @return Manager
     */
    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new Manager();
        }
        return self::$_instance;
    }
}
