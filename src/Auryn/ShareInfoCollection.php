<?php


namespace Auryn;


class ShareInfoCollection {

    /**
     * @var ShareInfo[]
     */
    protected $sharedInfoArray = array();

    private $sharedClassName;
    
    function __construct($sharedClassName, $instance, array $chainClassConstructors = array()) {
        $this->sharedClassName = $sharedClassName;
        $this->setSharedInstance($instance, $chainClassConstructors);
    }

    function clearSharedInstance() {
        foreach ($this->sharedInfoArray as $shareInfo) {
            $shareInfo->clearSharedInstance();
        }
    }

    function setSharedInstance($instance, array $chainClassConstructors = array()) {
        $this->sharedInfoArray[] = new ShareInfo($instance, $chainClassConstructors);
    }

    /**
     * Return the shared instance if it was shared for a $chainClassConstructors that
     * matches the current chainClassConstructors
     * @param array $chainClassConstructors
     * @return mixed|null
     */
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
    private function getBestSharedInfo($chainClassConstructors) {
        $bestInjectionInfo = null;

        $bestMatch = -1;
        foreach ($this->sharedInfoArray as $injectionInfo) {
            $score = $injectionInfo->getChainClassConstructors($chainClassConstructors);
            if ($score > $bestMatch){ 
                $bestInjectionInfo = $injectionInfo;
                $bestMatch = $score;
            }
        }

        return $bestInjectionInfo;
    }
}
