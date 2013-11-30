<?php


namespace Auryn;


class ShareInfoCollection {

    /**
     * @var ShareInfo[]
     */
    protected $sharedInfoArray = array();

    private $sharedClassName;
    
    function __construct($sharedClassName) {
        $this->sharedClassName = $sharedClassName;
    }

    function clearSharedInstance() {
        foreach ($this->sharedInfoArray as $shareInfo) {
            $shareInfo->clearSharedInstance();
        }
    }

    function setSharedInstance($instance, array $chainClassConstructors = array()) {
        $this->sharedInfoArray[] = new ShareInfo($instance, $chainClassConstructors);
    }

    function getSharedDefinition(array $chainClassConstructors) {
        $bestSharedInfo = $this->getBestSharedInfo($chainClassConstructors);
        
        if ($bestSharedInfo != null) {
            return $bestSharedInfo->getClassNameOrInstance();
        }

        return null;
    }

    /**
     * @param $chainClassConstructors
     * @return ShareInfo|null
     * @throws BuilderException
     */
    function getBestSharedInfo($chainClassConstructors) {

        if (count($this->sharedInfoArray) == 0) {
            return null;//$this->sharedInfoArray[0];
        }

        $bestInjectionInfo = null;

        $bestMatch = -1;
        foreach ($this->sharedInfoArray as $injectionInfo) {
            $score = $injectionInfo->getChainClassConstructors($chainClassConstructors);
            if ($score > $bestMatch){ 
                $bestInjectionInfo = $injectionInfo;
                $bestMatch = $score;
            }
        }
        
//        if ($bestInjectionInfo == null) {
//            $debugString = "Could not find definition for class in hierarchy:";
//
//            foreach ($chainClassConstructors as $className) {
//                $debugString .= "\t$className\n";
//            }
//
//            throw new BuilderException($debugString);
//        }

        return $bestInjectionInfo;
    }
}



?>