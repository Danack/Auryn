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
            throw new InjectionException(
                sprintf(Provider::E_DEFINITION_NOT_AVAILABLE_FOR_CLASS_CONSTRUCTOR_CHAIN_MESSAGE, implode( ' -> ', $chainClassConstructors)),
                Provider::E_DEFINITION_NOT_AVAILABLE_FOR_CLASS_CONSTRUCTOR_CHAIN_CODE
            );
        }

        return $bestInjectionInfo->getInjectionDefinition();
    }
}



?>