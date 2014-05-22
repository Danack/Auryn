<?php


namespace Auryn\Plugin;



class ClassConstructorChainProviderPlugin implements ProviderPlugin {

    /**
     * @var ProviderInfoCollection[]
     */
    private $injectionDefinitions = array();

    //TODO - these are not dependent on the CCC yet.
    private $aliases = array();

    /**
     * @var ProviderInfoCollection[]
     */
    protected $delegatedClasses = array();

    /**
     * @var ProviderInfoCollection[]
     */
    private $sharedClasses = array();

    /**
     * @var ProviderInfoCollection[]
     */
    protected $paramDefinitions = array();

    function getAlias($className, array $classConstructorChain) {
        $lowClass = strtolower($className);

        if (array_key_exists($lowClass, $this->aliases)) {
            return $this->aliases[$lowClass];
        }

        return null;
    }

    function shareInstanceIfRequired($provisionedObject, $classConstructorChain = array()) {
        $lowClass = strtolower(get_class($provisionedObject));

        if (isset($this->sharedClasses[$lowClass])) {
            $delegateProviderInfo = $this->sharedClasses[$lowClass]->getExactMatchingInfo($classConstructorChain);

            if ($delegateProviderInfo != null) {
                /**
                 * @var $delegateProviderInfo ProviderInfo
                 */
                $delegateProviderInfo->setValue($provisionedObject);
            }
        }
    }

    function getShared($className, array $classConstructorChain) {
        $lowClass = strtolower($className);

        if (isset($this->sharedClasses[$lowClass])) {
            $sharedInstanceInfo = $this->sharedClasses[$lowClass]->getBestMatchingInfo($classConstructorChain);

            if ($sharedInstanceInfo != null) {
                $sharedInstance = $sharedInstanceInfo->getValue();
                return $sharedInstance;
            }
        }
        return null;
    }

    function getDefinition($className, array $classConstructorChain) {
        $lowClass = strtolower($className);
        $definition = null;

        if (isset($this->injectionDefinitions[$lowClass])) {
            $providerInfo = $this->injectionDefinitions[$lowClass]->getBestMatchingInfo($classConstructorChain);
            if ($providerInfo != null) {
                return $providerInfo->getValue();
            }
        }

        return null;
    }

    function shareClass($classNameOrInstance, array $classConstructorChain = array()) {
        $lowClass = strtolower($classNameOrInstance);

        if (isset($this->aliases[$lowClass]) == TRUE) {
            $lowClass = strtolower($this->aliases[$lowClass]);
        }

        if (array_key_exists($lowClass, $this->sharedClasses) == FALSE){
            $this->sharedClasses[$lowClass] = new ProviderInfoCollection(null, $classConstructorChain);
        }
    }

    function shareInstance($provisionedObject, array $classConstructorChain = array()) {

        $lowClass = strtolower(get_class($provisionedObject));

        if (isset($this->aliases[$lowClass])) {
            // You cannot share an instance of a class that has already been aliased to another class.
            throw new \Auryn\InjectionException(
                sprintf(
                    \Auryn\AurynInjector::$errorMessages[\Auryn\AurynInjector::E_ALIASED_CANNOT_SHARE],
                    $lowClass,
                    $this->aliases[$lowClass]
                ),
                \Auryn\Provider::E_ALIASED_CANNOT_SHARE
            );
        }

        if (array_key_exists($lowClass, $this->sharedClasses) == false) {
            $this->sharedClasses[$lowClass] = new ProviderInfoCollection($provisionedObject, $classConstructorChain);
        }
        else{
            $this->sharedClasses[$lowClass]->addInfo($provisionedObject, $classConstructorChain);
        }
    }

    private function validateInjectionDefinition(array $injectionDefinition) {
        foreach ($injectionDefinition as $paramName => $value) {
            if ($paramName[0] !== \Auryn\Provider::RAW_INJECTION_PREFIX && !is_string($value)) {
                throw new \Auryn\BadArgumentException(
                    sprintf(
                        \Auryn\AurynInjector::$errorMessages[\Auryn\AurynInjector::E_RAW_PREFIX],
                        $paramName,
                        $paramName
                    ),
                    \Auryn\Provider::E_RAW_PREFIX
                );
            }
        }
    }

    /**
     * Defines custom instantiation parameters for the specified class
     *
     * @param string $className The class whose instantiation we wish to define
     * @param array $injectionDefinition An array mapping parameter names to classes and/or raw values
     * @param array $chainClassConstructors
     * @return $this
     */
    public function define($className, array $injectionDefinition, array $chainClassConstructors = array()) {
        $this->validateInjectionDefinition($injectionDefinition);
        $lowClass = strtolower($className);
        if (array_key_exists($lowClass, $this->injectionDefinitions) == false) {
            $this->injectionDefinitions[$lowClass] = new ProviderInfoCollection($injectionDefinition, $chainClassConstructors);
        }
        else{
            $this->injectionDefinitions[$lowClass]->addInfo($injectionDefinition, $chainClassConstructors);
        }

        return $this;
    }

    /**
     * Forces re-instantiation of a shared class the next time it is requested
     *
     * @param mixed $classNameOrInstance The class name for which an existing share should be cleared for re-instantiation
     */
    function refresh($classNameOrInstance) {
        if (is_object($classNameOrInstance)) {
            $classNameOrInstance = get_class($classNameOrInstance);
        }

        $className = strtolower($classNameOrInstance);

        if (isset($this->sharedClasses[$className])) {
            $this->sharedClasses[$className] = NULL;
        }
    }

    /**
     * Defines an alias class for all occurrences of a given typehint
     *
     * Use this method to specify implementation classes for interface and abstract class typehints.
     *
     * @param string $typehintToReplace The typehint to replace
     * @param string $alias The implementation class name
     * @throws \Auryn\InjectionException
     */
    function alias($typehintToReplace, $alias) {
        $lowTypehint = strtolower($typehintToReplace);
        $lowAlias = strtolower($alias);

        if (isset($this->sharedClasses[$lowTypehint])) {
            $sharedClassName = strtolower(get_class($this->sharedClasses[$lowTypehint]));
            throw new \Auryn\InjectionException(
                sprintf(
                    \Auryn\AurynInjector::$errorMessages[\Auryn\AurynInjector::E_SHARED_CANNOT_ALIAS],
                    $sharedClassName,
                    $alias
                ),
                \Auryn\Provider::E_SHARED_CANNOT_ALIAS
            );
        } else {
            $this->aliases[$lowTypehint] = $alias;
        }

        if (array_key_exists($lowTypehint, $this->sharedClasses)) {
            $this->sharedClasses[$lowAlias] = $this->sharedClasses[$lowTypehint];
            unset($this->sharedClasses[$lowTypehint]);
        }
    }


    /**
     * Unshares the specified class or the class of the specified instance
     *
     * @param mixed $classNameOrInstance The class or object to unshare
     * @return $this
     */
    public function unshare($classNameOrInstance) {
        $className = is_object($classNameOrInstance)
            ? get_class($classNameOrInstance)
            : $classNameOrInstance;
        $className = strtolower($className);

        unset($this->sharedClasses[$className]);

        return $this;
    }

    function isDelegated($lowClass, array $classConstructorChain) {
        $lowClass = strtolower($lowClass);
        if (isset($this->delegatedClasses[$lowClass])) {
            $delegateProviderInfo = $this->delegatedClasses[$lowClass]->getBestMatchingInfo($classConstructorChain);
            if ($delegateProviderInfo) {
                return true;
            }
        }

        return false;
    }

    function getDelegated($lowClass, array $classConstructorChain) {
        $lowClass = strtolower($lowClass);

        if (isset($this->delegatedClasses[$lowClass])) {
            return $this->delegatedClasses[$lowClass]->getBestMatchingInfo($classConstructorChain);
        }

        return null;
    }

    /**
     * Delegates the creation of $class to $callable. Passes $class to $callable as the only argument
     *
     * @param string $className
     * @param callable $callable
     * @param array $classConstructorChain
     * @param array $args
     * @throws \Auryn\BadArgumentException
     * @return \Auryn\Provider Returns the current instance
     */
    public function delegate($className, $callable, array $classConstructorChain, array $args = array()) {
        if (is_callable($callable)
            || (is_string($callable) && method_exists($callable, '__invoke'))
            || (is_array($callable) && isset($callable[0], $callable[1]) && method_exists($callable[0], $callable[1]))
        ) {
            $delegate = array($callable, $args);
        } else {
            throw new \Auryn\BadArgumentException(
                sprintf(\Auryn\AurynInjector::$errorMessages[\Auryn\AurynInjector::E_DELEGATE_ARGUMENT], __CLASS__),
                \Auryn\AurynInjector::E_DELEGATE_ARGUMENT
            );
        }

        $lowClass = strtolower($className);

        if (array_key_exists($lowClass, $this->delegatedClasses) == false) {
            $this->delegatedClasses[$lowClass] = new ProviderInfoCollection($delegate, $classConstructorChain);
        }
        else{
            $this->delegatedClasses[$lowClass]->addInfo($delegate, $classConstructorChain);
        }
    }

    /**
     * Assign a global default value for all parameters named $paramName
     *
     * Global parameter definitions are only used for parameters with no typehint, pre-defined or
     * call-time definition.
     * @param string $paramName
     * @param mixed $value
     * @param array $chainClassConstructors
     */
    function defineParam($paramName, $value, array $chainClassConstructors = array()) {
        if (array_key_exists($paramName, $this->paramDefinitions) == false) {
            $this->paramDefinitions[$paramName] = new ProviderInfoCollection($value, $chainClassConstructors);
        }
        else{
            $this->paramDefinitions[$paramName]->addInfo($value, $chainClassConstructors);
        }
    }

    function getParamDefine($paramName, array $classConstructorChain) {
        if (array_key_exists($paramName, $this->paramDefinitions)) {

            $isSet = false;
            $paramProviderInfo = $this->paramDefinitions[$paramName]->getBestMatchingInfo($classConstructorChain, $isSet);

            if ($isSet != null) {
                $value = $paramProviderInfo->getValue();
                return array(true, $value);
            }
        }

        return array(false, null);
    }

    /**
     * Register a mutator callable to modify (prepare) objects after instantiation
     *
     * Any callable or provisionable executable may be specified. Preparers are passed two
     * arguments: the instantiated object to be modified and the current Injector instance.
     *
     * @param string $classInterfaceOrTraitName
     * @param mixed $executable Any callable or provisionable executable method
     */
    public function prepare($classInterfaceOrTraitName, $executable) {

    }

    public function getPrepareExecutable($lowClass) {
        return null;
    }

    public function getPrepareExecutableForInterfaces($interfacesImplemented)
    {
        return [];
    }

    function getParamDelegation($paramName, array $classConstructorChain) {
    }

    public function delegateParam($paramName, $callable, array $classConstructorChain, array $args = array()) {
    }


}




 