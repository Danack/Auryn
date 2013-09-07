<?php

namespace Auryn;

/**
 * A catch-all exception for DIC instantiation errors
 */
class InjectionException extends \RuntimeException {

    public function __construct($message = "", $code = 0, array $classConstructorChain = array(), \Exception $previous = null) {
    
        if (count($classConstructorChain) > 0) {
            $message .= " Constructor chain is ".implode('->', $classConstructorChain);
        }

        parent::__construct($message, $code, $previous);
    }
    
    
}
