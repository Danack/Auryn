<?php


namespace Auryn;



class ShareInfo {

    private $classNameOrInstance;
    private $chainClassConstructors;

    public function __construct($classNameOrInstance, array $chainClassConstructors) {
        $this->classNameOrInstance = $classNameOrInstance;
        $this->chainClassConstructors = $chainClassConstructors;
    }

    public function clearSharedInstance(){
        $this->classNameOrInstance = null;
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

    public function getClassNameOrInstance(){
        return $this->classNameOrInstance;
    }

}



?>