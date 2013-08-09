<?php


namespace Auryn;



class InjectionInfo {

    private $injectionDefinition;
    private $hierarchyMatch;

    public function __construct(array $injectionDefinition, array $hierarchyMatch) {
        $this->injectionDefinition = $injectionDefinition;
        $this->hierarchyMatch = $hierarchyMatch;
    }
    
    public function getHierarchyMatch(array $classNameHierarchy) {

        $usedIndex = 0;
        $score = 0;

        foreach ($this->hierarchyMatch as $className) {
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