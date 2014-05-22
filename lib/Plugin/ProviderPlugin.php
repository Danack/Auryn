<?php


namespace Auryn\Plugin;


interface ProviderPlugin {


    /**
     * Defines an alias class for all occurrences of a given typehint
     *
     * Use this method to specify implementation classes for interface and abstract class typehints.
     *
     * @param string $typehintToReplace The typehint to replace
     * @param string $alias The implementation class name
     * @throws \Auryn\InjectionException
     */
    function alias($typehintToReplace, $alias);
    
    /** Gets the alias class that should be created instead of $className
     * @param $className
     * @param array $classConstructorChain
     * @return mixed
     */
    function getAlias($className, array $classConstructorChain);

    function shareInstanceIfRequired($provisionedObject, $classConstructorChain = array());

    /** Get the shared instance of $className or null if none has been set.
     * @param $className
     * @param array $classConstructorChain
     * @return mixed
     */
    function getShared($className, array $classConstructorChain);

    /**
     * Return an array that contains a mixture of classnames and scalar values to
     * be used as the parameters for creating an object of type $className
     *
     * Example return array:
     * [ 'Logger' => 'FileLogger',
     *   ':maxSize' => 256
     * ]
     *
     * @param $className
     * @param array $classConstructorChain
     * @return array|null
     */
    function getDefinition($className, array $classConstructorChain);

    /**
     * Shares the specified class across the Injector context
     *
     * @param mixed $classNameOrInstance The class or object to share
     * @param array $classConstructorChain
     */
    function shareClass($classNameOrInstance, array $classConstructorChain = array());
    
    /**
     * @param $instance
     * @param array $classConstructorChain
     * @return mixed
     */
    function shareInstance($instance, array $classConstructorChain = array());

    /**
     * Unshares the specified class or the class of the specified instance
     *
     * @param mixed $classNameOrInstance The class or object to unshare
     * @return $this
     */
    public function unshare($classNameOrInstance);
    
    /**
     * @param $lowClass
     * @param array $classConstructorChain
     * @return mixed
     */
    function isDelegated($lowClass, array $classConstructorChain);

    /** Get the delegated method for creating a class.
     * @param $lowClass
     * @param array $classConstructorChain
     * @return mixed The return should be an array containing the callable and
     * the args for that callable.
     */
    function getDelegated($lowClass, array $classConstructorChain);

    function getParamDelegation($paramName, array $classConstructorChain);
    
    /**
     * Delegates the creation of $className instances to $callable
     *
     * @param string $className
     * @param callable $callable
     * @param array $classConstructorChain
     * @param array $args
     * @return
     */
    function delegate($className, $callable, array $classConstructorChain, array $args = array());

    /**
     * Defines a custom injection definition for the specified class
     *
     * @param string $className
     * @param array $injectionDefinition An associative array matching constructor params to values
     * @throws \Auryn\BadArgumentException On missing raw injection prefix
     * @return \Auryn\Provider Returns the current instance
     */
    public function define($className, array $injectionDefinition);
    
    public function delegateParam($paramName, $callable, array $classConstructorChain, array $args = array());
    
    /**
     * Assign a global default value for all parameters named $paramName
     *
     * Global parameter definitions are only used for parameters with no typehint, pre-defined or
     * call-time definition.
     *
     * @param string $paramName The parameter name for which this value applies
     * @param mixed $value The value to inject for this parameter name
     * @param array $classConstructorChain
     * @return
     */
    function defineParam($paramName, $value, array $classConstructorChain = array());

    /**
     * Get a parameter definition by name.
     *
     * @param $paramName
     * @param array $classConstructorChain
     * @return mixed
     */
    function getParamDefine($paramName, array $classConstructorChain);


    /**
     * Forces re-instantiation of a shared class the next time it is requested
     *
     * @param mixed $classNameOrInstance The class name for which an existing share should be cleared for re-instantiation
     */
    function refresh($classNameOrInstance);

    /**
     * Register a mutator callable to modify (prepare) objects after instantiation
     *
     * Any callable or provisionable executable may be specified. Preparers are passed two
     * arguments: the instantiated object to be modified and the current Injector instance.
     *
     * @param string $classInterfaceOrTraitName
     * @param mixed $executable Any callable or provisionable executable method
     */
    public function prepare($classInterfaceOrTraitName, $executable);

    public function getPrepareExecutable($lowClass);

    public function getPrepareExecutableForInterfaces($interfacesImplemented);
}

 