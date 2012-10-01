<?php

namespace Auryn;
 
interface Injector {

    /**
     * Auto-injects class constructor dependencies
     *
     * @param string $className
     * @param array $customDefinition
     */
    function make($className, array $customDefinition = NULL);
    
    /**
     * Defines custom instantiation parameters for the specified class
     * 
     * @param string $className
     * @param array $injectionDefinition
     */
    function define($className, array $injectionDefinition);
    
    /**
     * Retrieves the assigned definition for the specified class
     * 
     * @param string $className
     * 
     * @return array
     */
    function getDefinition($className);
    
    /**
     * Determines if an injection definition exists for the specified class
     * 
     * @param string $className
     * @return bool
     */
    function isDefined($className);
    
    /**
     * Defines multiple custom instantiation parameters at once
     * 
     * @param mixed $iterable The variable to iterate over: an array, StdClass
     *                        or ArrayAccess instance
     */
    function defineAll($iterable);
    
    /**
     * Clears a previously-defined injection definition
     * 
     * @param string $className
     */
    function clearDefinition($className);
    
    /**
     * Clears all injection definitions from the container
     */
    function clearAllDefinitions();
    
    /**
     * Defines an implementation class for all occurrences of a given interface or abstract class
     * 
     * @param string $nonConcreteType
     * @param string $className
     */
    function implement($nonConcreteType, $className);
    
    /**
     * Retrieves the assigned implementation for the non-concrete type
     * 
     * @param string $nonConcreteType
     * 
     * @return string Returns the assigned concrete implementation class
     */
    function getImplementation($nonConcreteType);
    
    /**
     * Determines if an implementation definition exists for the non-concrete type
     * 
     * @param string $nonConcreteType
     * 
     * @return bool
     */
    function isImplemented($nonConcreteType);
    
    /**
     * Defines multiple type implementations at one time
     * 
     * @param mixed $iterable The variable to iterate over: an array, StdClass or Traversable
     * 
     * @return int Returns the number of implementations stored by the operation.
     */
    public function implementAll($iterable);
    
    /**
     * Clears an existing implementation class for the specified non-concrete type
     * 
     * @param string $nonConcreteType
     */
    function clearImplementation($nonConcreteType);
    
    /**
     * Clears all existing non-concrete implementations
     */
    function clearAllImplementations();
    
    /**
     * Shares the specified class
     * 
     * @param mixed $classNameOrInstance
     */
    function share($classNameOrInstance);
    
    /**
     * Shares all specified classes/instances
     * 
     * @param mixed $arrayOrTraversableObject
     */
    function shareAll($arrayOrTraversableObject);
    
    /**
     * Determines if a given class name is marked as shared
     * 
     * @param string $className
     * 
     * @return bool
     */
    function isShared($className);
    
    /**
     * Forces re-instantiation of a shared class the next time it is requested
     * 
     * @param string $className
     */
    function refresh($className);
    
    /**
     * Unshares the specified class
     * 
     * @param string $className
     */
    function unshare($className);
}
