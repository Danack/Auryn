<?php

namespace Auryn;

/**
 * The Provider exposes functionality for defining context-wide instantiation, instance sharing,
 * implementation and delegation rules for injecting and automatically provisioning deeply nested
 * class dependencies.
 */
class Provider extends AurynInjector {


    public function __construct(ReflectionStorage $reflectionStorage = NULL) {
        $defaultPlugin = new Plugin\StandardProviderPlugin();
        parent::__construct($defaultPlugin, $reflectionStorage);
    }

    /**
     * Defines an alias class name for all occurrences of a given typehint
     *
     * @param string $typehintToReplace
     * @param string $alias
     * @throws InjectionException
     * @throws BadArgumentException On non-empty string argument
     * @return \Auryn\Provider Returns the current instance
     */
    public function alias($typehintToReplace, $alias) {
        if (empty($typehintToReplace) || !is_string($typehintToReplace)) {
            throw new BadArgumentException(
                \Auryn\AurynInjector::$errorMessages[\Auryn\AurynInjector::E_NON_EMPTY_STRING_ALIAS],
                \Auryn\AurynInjector::E_NON_EMPTY_STRING_ALIAS
            );
        } elseif (empty($alias) || !is_string($alias)) {
            throw new BadArgumentException(
                \Auryn\AurynInjector::$errorMessages[\Auryn\AurynInjector::E_NON_EMPTY_STRING_ALIAS],
                \Auryn\AurynInjector::E_NON_EMPTY_STRING_ALIAS
            );
        }

        $this->plugin->alias($typehintToReplace, $alias);

        return $this;
    }

    /**
     * Stores a shared instance of the specified class
     *
     * If an instance of the class is specified, it will be stored and shared
     * for calls to `Provider::make` for that class until the shared instance
     * is manually removed or refreshed.
     *
     * If a string class name is specified, the Provider will mark the class
     * as "shared" and the next time the Provider is used to instantiate the
     * class it's instance will be stored and shared.
     *
     * @param mixed $classNameOrInstance
     * @throws InjectionException
     * @throws BadArgumentException
     * @return \Auryn\Provider Returns the current instance
     */
    public function share($classNameOrInstance) {
        if (is_string($classNameOrInstance)) {
            $this->plugin->shareClass($classNameOrInstance);
        } elseif (is_object($classNameOrInstance)) {
            $this->plugin->shareInstance($classNameOrInstance);
        } else {
            throw new BadArgumentException(
                sprintf(\Auryn\AurynInjector::$errorMessages[self::E_SHARE_ARGUMENT], __CLASS__, gettype($classNameOrInstance)),
                self::E_SHARE_ARGUMENT
            );
        }
        return $this;
    }

    /**
     * Defines a custom injection definition for the specified class
     *
     * @param string $className
     * @param array $injectionDefinition An associative array matching constructor params to values
     * @throws \Auryn\BadArgumentException On missing raw injection prefix
     * @return \Auryn\Provider Returns the current instance
     */
    public function define($className, array $injectionDefinition){
        $this->plugin->define($className, $injectionDefinition);
    }
    
    /**
     * Assign a global default value for all parameters named $paramName
     *
     * Global parameter definitions are only used for parameters with no typehint, pre-defined or
     * call-time definition.
     *
     * @param string $paramName The parameter name for which this value applies
     * @param mixed $value The value to inject for this parameter name
     * @return \Auryn\Provider Returns the current instance
     */
    public function defineParam($paramName, $value) {
        $this->plugin->defineParam($paramName, $value, array());
    }



    /**
     * Unshares the specified class (or the class of the specified object)
     *
     * @param mixed $classNameOrInstance Class name or object instance
     * @return \Auryn\Provider Returns the current instance
     */
    public function unshare($classNameOrInstance) {
        $this->plugin->unshare($classNameOrInstance);

        return $this;
    }

    /**
     * Forces re-instantiation of a shared class the next time it's requested
     *
     * @param mixed $classNameOrInstance Class name or instance
     * @return \Auryn\Provider Returns the current instance
     */
    public function refresh($classNameOrInstance) {
        $this->plugin->refresh($classNameOrInstance);

        return $this;
    }

    /**
     * Delegates the creation of $class to $callable. Passes $class to $callable as the only argument
     *
     * @param string $className
     * @param callable $callable
     * @param array $args [optional]
     * @throws BadArgumentException
     * @return \Auryn\Provider Returns the current instance
     */
    public function delegate($className, $callable, array $args = array()) {
        $this->plugin->delegate($className, $callable, [], $args);

        return $this;
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
        $this->plugin->prepare($classInterfaceOrTraitName, $executable);

        return $this;
    }
    
    public function delegateParam($className, $callable, array $args = array()) {
        $this->plugin->delegateParam($className, $callable, [], $args);   
    }

}
