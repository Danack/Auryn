<?php


namespace Auryn;


class InjectionInfoCollection {

    /**
     * @var InjectionInfo[]
     */
    protected $injectionInfoArray = array();

    function __construct(array $injectionDefinition, array $chainClassConstructors) {
        $this->addInjectionDefinition($injectionDefinition, $chainClassConstructors);
    }

    function addInjectionDefinition(array $injectionDefinition, array $chainClassConstructors) {
        $this->injectionInfoArray[] = new InjectionInfo($injectionDefinition, $chainClassConstructors);
    }

    function getInjectionDefinition(array $chainClassConstructors) {
        $bestInjectionInfo = null;

        $bestMatch = -1;
        foreach ($this->injectionInfoArray as $injectionInfo) {
            if ($injectionInfo->getChainClassConstructors($chainClassConstructors) > $bestMatch){ 
                $bestInjectionInfo = $injectionInfo;
            }
        }
        
        if ($bestInjectionInfo == null) {
            $debugString = "Could not find definition for class in hierarchy:";

            foreach ($chainClassConstructors as $className) {
                $debugString .= "\t$className\n";
            }
            
            throw new BuilderException($debugString);
        }

        return $bestInjectionInfo->getInjectionDefinition();
    }
}



?>