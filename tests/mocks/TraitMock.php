<?php

namespace MockaMocks;

trait TraitMock {

    abstract function abstractTraitMethod();

    public function traitMethod() {

    }

    public function bar () {
        return 'traitbar';
    }

    public function barReturnType(): string {
        return 'barbar';
    }

    public function barVoid(): void {
    }
}
