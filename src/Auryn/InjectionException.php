<?php

namespace Auryn;

/**
 * A catch-all exception for DIC instantiation errors
 */
class InjectionException extends \RuntimeException {

    public function __construct(array $classConstructorChain = array(), $message = "", $code = 0, \Exception $previous = NULL) {
    
        if (count($classConstructorChain) > 0) {
            $message .= " Constructor chain is ".implode('->', $classConstructorChain);
        }

        parent::__construct($message, $code, $previous);
    }
    
    
}
