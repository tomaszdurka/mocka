<?php

namespace Mocka;

class Invocations {

    /** @var Invocation[] */
    private $_list;

    /**
     * @param Invocation $invocation
     */
    public function add(Invocation $invocation) {
        $this->_list[] = $invocation;
    }

    /**
     * @param int $number
     * @return Invocation
     * @throws Exception
     */
    public function get($number) {
        if (array_key_exists($number, $this->_list)) {
            throw new Exception('Invocation not found');
        }
        return $this->_list[$number];
    }

    /**
     * @return int
     */
    public function getCount() {
        return count($this->_list);
    }

    /**
     * @return array[]
     */
    public function getArguments() {
        return array_map(function (Invocation $invocation) {
            return $invocation->getArguments();
        }, $this->_list);
    }
}
