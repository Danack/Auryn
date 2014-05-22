<?php

namespace Auryn\Plugin;

use Auryn\BadArgumentException;

class StandardProviderPlugin implements ProviderPlugin {

    protected $injectionDefinitions = array();
    protected $aliases = array();
    protected $sharedClasses = array();
    protected $delegatedClasses = array();
    protected $paramDefinitions = array();
    protected $prepares = array();

    function getAlias($className, array $classConstructorChain) {
        $lowClass = strtolower($className);

        if (array_key_exists($lowClass, $this->aliases)) {
            return $this->aliases[$lowClass];
        }

        return null;
    }

    function shareInstanceIfRequired($provisionedObject, $classConstructorChain = array()) {
        if ($this->isSharable(get_class($provisionedObject), $classConstructorChain)) {
            $this->shareInstance($provisionedObject, array());
        }
    }

    function isSharable($className, array $classConstructorChain) {
        $lowClass = strtolower($className);
        return array_key_exists($lowClass, $this->sharedClasses);
    }

    function getShared($className, array $classConstructorChain) {
        $lowClass = ltrim(strtolower($className), '\\');

        if (array_key_exists($lowClass, $this->sharedClasses) == true) {
            if ($this->sharedClasses == null) {
                return $lowClass;
            }
            return $this->sharedClasses[$lowClass];
        }

        return null;
    }

    /**
     * @param $className
     * @param array $classConstructorChain
     * @return array|null
     */
    function getDefinition($className, array $classConstructorChain) {
        $lowClass = strtolower($className);

        if (isset($this->injectionDefinitions[$lowClass])) {
            return $this->injectionDefinitions[$lowClass];
        }

        return null;
    }


    /**
     * Shares the specified class across the Injector context
     *
     * @param string $className The class or object to share
     */
    function shareClass($className, array $classConstructorChain = array()) {
        
            $lowClass = ltrim(strtolower($className),'\\');
            $lowClass = isset($this->aliases[$lowClass])
                ? strtolower($this->aliases[$lowClass])
                : $lowClass;

            $this->sharedClasses[$lowClass] = isset($this->sharedClasses[$lowClass])
                ? $this->sharedClasses[$lowClass]
                : NULL;


        return $this;
    }

    function shareInstance($instance, array $classConstructorChain = array()) {
        $lowClass = strtolower(get_class($instance));

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

        $this->sharedClasses[$lowClass] = $instance;
    }

    private function validateInjectionDefinition(array $injectionDefinition) {
        foreach ($injectionDefinition as $paramName => $value) {
            if ($paramName[0] !== \Auryn\AurynInjector::RAW_INJECTION_PREFIX && !is_string($value)) {
                throw new BadArgumentException(
                    sprintf(\Auryn\AurynInjector::$errorMessages[\Auryn\AurynInjector::E_RAW_PREFIX], $paramName, $paramName),
                    \Auryn\AurynInjector::E_RAW_PREFIX
                );
            }
        }
    }

    /**
     * Defines a custom injection definition for the specified class
     *
     * @param string $className
     * @param array $injectionDefinition An associative array matching constructor params to values
     * @return void
     * @throws \Auryn\BadArgumentException On missing raw injection prefix
     */
    public function define($className, array $injectionDefinition) {
        $this->validateInjectionDefinition($injectionDefinition);
        $lowClass = ltrim(strtolower($className), '\\');
        $this->injectionDefinitions[$lowClass] = $injectionDefinition;
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
        $className = ltrim(strtolower($classNameOrInstance), '\\');
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
        $lowTypehint = ltrim(strtolower($typehintToReplace), '\\');
        $lowAlias = strtolower($alias);

        if (isset($this->sharedClasses[$lowTypehint])) {
            $sharedClassName = strtolower(get_class($this->sharedClasses[$lowTypehint]));
            throw new \Auryn\InjectionException(
                sprintf(\Auryn\AurynInjector::$errorMessages[\Auryn\AurynInjector::E_SHARED_CANNOT_ALIAS], $sharedClassName, $alias),
                \Auryn\AurynInjector::E_SHARED_CANNOT_ALIAS
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
        $className = ltrim(strtolower($className), '\\');

        unset($this->sharedClasses[$className]);
    }


    function isDelegated($lowClass, array $classConstructorChain) {
        return isset($this->delegatedClasses[$lowClass]);
    }

    function getDelegated($lowClass, array $classConstructorChain) {
        return $this->delegatedClasses[$lowClass];
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
        if ($this->canExecute($callable)) {
            $delegate = array($callable, $args);
        } else {
            throw new BadArgumentException(
                sprintf(\Auryn\AurynInjector::$errorMessages[\Auryn\AurynInjector::E_DELEGATE_ARGUMENT], __CLASS__),
                \Auryn\AurynInjector::E_DELEGATE_ARGUMENT
            );
        }

        $lowClass = ltrim(strtolower($className), '\\');
        $this->delegatedClasses[$lowClass] = $delegate;

        return $this;
    }

    /**
     * Assign a global default value for all parameters named $paramName
     *
     * Global parameter definitions are only used for parameters with no typehint, pre-defined or
     * call-time definition.
     *
     * @param string $paramName The parameter name for which this value applies
     * @param mixed $value The value to inject for this parameter name
     * @param array $classConstructorChain
     * @return \Auryn\Provider Returns the current instance
     */
    function defineParam($paramName, $value, array $classConstructorChain = array()) {
        $this->paramDefinitions[$paramName] = $value;

        return $this;
    }

    function getParamDefine($paramName, array $classConstructorChain) {
        if (array_key_exists($paramName, $this->paramDefinitions)) {
            return array(true, $this->paramDefinitions[$paramName]);
        }

        return array(false, null);
    }

    /**
     * Register a mutator callable to modify objects after instantiation
     *
     * @param string $classInterfaceOrTraitName
     * @param mixed $executable Any callable or provisionable executable method
     * @throws \Auryn\BadArgumentException
     * @return \Auryn\Provider Returns the current instance
     */
    public function prepare($classInterfaceOrTraitName, $executable) {
        if (!$this->canExecute($executable)) {
            throw new BadArgumentException(
                \Auryn\AurynInjector::$errorMessages[\Auryn\AurynInjector::E_CALLABLE],
                \Auryn\AurynInjector::E_CALLABLE
            );
        }

        $normalizedName = ltrim(strtolower($classInterfaceOrTraitName), '\\');
        $this->prepares[$normalizedName] = $executable;

        return $this;
    }

    private function canExecute($exe) {
        if (is_callable($exe)) {
            return TRUE;
        }

        if (is_string($exe) && method_exists($exe, '__invoke')) {
            return TRUE;
        }

        if (is_array($exe) && isset($exe[0], $exe[1]) && method_exists($exe[0], $exe[1])) {
            return TRUE;
        }

        return FALSE;
    }

    public function getPrepareExecutable($lowClass) {
        if (array_key_exists($lowClass, $this->prepares)) {
            return $this->prepares[$lowClass];
        }

        return null;
    }

    function getPrepareExecutableForInterfaces($interfacesImplemented) {
        return array_intersect_key($this->prepares, $interfacesImplemented);
    }
    
}




 




 