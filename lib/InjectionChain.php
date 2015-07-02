<?php


namespace Auryn;


class InjectionChain {
    
    private $inProgressMakes;
    
    public function __construct($inProgressMakes) {
        $this->inProgressMakes = $inProgressMakes;
    }
    
    public function getInProgressMakes() {
        return $this->inProgressMakes;
    }
}

