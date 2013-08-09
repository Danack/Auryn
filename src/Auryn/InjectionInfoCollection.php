<?php


namespace Auryn;


class InjectionInfoCollection {

    /**
     * @var InjectionInfo[]
     */
    protected $injectionInfoArray = array();


    function addInjectionDefintion(array $injectionDefinition, array $hierarchyMatch) {
        $this->injectionInfoArray[] = new InjectionInfo($injectionDefinition, $hierarchyMatch);
    }


    function getInjectionDefinition(array $classHierarchy) {

        if (count($this->injectionInfoArray) == 0) {
            return $this->injectionInfoArray[0];
        }
        
        $bestInjectionInfo = null;

        $bestMatch = -1;
        foreach ($this->injectionInfoArray as $injectionInfo) {
            if ($injectionInfo->getHierarchyMatch($classHierarchy) > $bestMatch){ 
                $bestInjectionInfo = $injectionInfo;
            }
        }
        
        if ($bestInjectionInfo == null) {
            $debugString = "Could not find definition for class in hierarchy:";

            foreach ($classHierarchy as $className) {
                $debugString .= "\t$className\n";
            }
            
            throw new BuilderException($debugString);
        }

        return $bestInjectionInfo->getInjectionDefinition();
    }
}



?>