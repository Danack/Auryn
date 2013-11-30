<?php


namespace Auryn;



class InjectionInfo {

    private $injectionDefinition;
    private $chainClassConstructors;

    public function __construct(array $injectionDefinition, array $chainClassConstructors) {
        $this->injectionDefinition = $injectionDefinition;
        $this->chainClassConstructors = $chainClassConstructors;
    }
    
    public function getChainClassConstructors(array $classNameHierarchy) {

        $usedIndex = 0;
        $score = 0;

        foreach ($this->chainClassConstructors as $className) {
            $found = false;
            
            for ($x = $usedIndex; $x<count($classNameHierarchy) ; $x++) {
                if ($classNameHierarchy[$x] == $className) {
                    $usedIndex = $x;
                    $score += 1;
                    $found = true;
                }
            }
            if ($found == false) {
                //The required classname was not found in the class hierarchy.
                return -1;
            }
        }

        return $score;
    }

    public function getInjectionDefinition(){
        return $this->injectionDefinition;
    }

}



?>