<?php


class TestSharingClass{
}

class AliasedTestSharingClass {
}


class Logger {

    private $logLevel;
    
    function __construct($logLevel) {
        $this->logLevel = $logLevel;
    }

    function getLogLevel(){
        return $this->logLevel;
    }

    public function info($message, array $context = array()){}
    public function warning($message, array $context = array()){}
}

class RequiresLoggerDependency1{

    public $logger;
    
    function __construct(Logger $logger){
        $this->logger = $logger;
    }
}

class RequiresLogger1{

    public $requiresLoggerDependency1;
    public $logger;
    
    public function __construct(Logger $logger, RequiresLoggerDependency1 $requiresLoggerDependency1){
        $this->requiresLoggerDependency1 = $requiresLoggerDependency1;
        $this->logger = $logger;
    }
}


class RequiresLoggerDependency2{

    public $logger;

    function __construct(Logger $logger){
        $this->logger = $logger;
    }
}

class RequiresLogger2{

    public $requiresLoggerDependency2;
    public $logger;

    public function __construct(Logger $logger, RequiresLoggerDependency2 $requiresLoggerDependency2){
        $this->requiresLoggerDependency2 = $requiresLoggerDependency2;
        $this->logger = $logger;
    }
}




class WidgetWithParams {

    public $name = null;

    function __construct($name){
        $this->name = $name;
    }
}

class UsesWidgetWithParams1{
 
    public $widget;
    
    public function __construct(WidgetWithParams $widget) {
        $this->widget = $widget;
    }
}

class UsesWidgetWithParams2 {
    public $widget;

    public function __construct(WidgetWithParams $widget) {
        $this->widget = $widget;
    }
}

class UsesWidget {
    
    public $widget;
    
    public function __construct(WidgetWithParams $widget) {
        $this->widget = $widget;
    }
}

class UsesWidgetWithParamsOnceRemoved1 {

    public $usesWidget;
    
    function __construct(UsesWidget $usesWidget) {
        $this->usesWidget = $usesWidget;
    }
}

class UsesWidgetWithParamsOnceRemoved2 {

    public $usesWidget;

    function __construct(UsesWidget $usesWidget) {
        $this->usesWidget = $usesWidget;
    }
}



class InaccessibleExecutableClassMethod {
    private function doSomethingPrivate() {
        return 42;
    }
    protected function doSomethingProtected() {
        return 42;
    }
}

class InaccessibleStaticExecutableClassMethod {
    private static function doSomethingPrivate() {
        return 42;
    }
    protected static function doSomethingProtected() {
        return 42;
    }
}

class ClassWithStaticMethodThatTakesArg {
    static function doSomething($arg) {
        return 1 + $arg;
    }
}

class RecursiveClass1 {
    function __construct(RecursiveClass2 $dep) {}
}

class RecursiveClass2 {
    function __construct(RecursiveClass1 $dep) {}
}

class RecursiveClassA {
    function __construct(RecursiveClassB $b) {}
}

class RecursiveClassB {
    function __construct(RecursiveClassC $c) {}
}

class RecursiveClassC {
    function __construct(RecursiveClassA $a) {}
}

class DependsOnCyclic {
    function __construct(RecursiveClassA $a) {}
}

interface SharedAliasedInterface {
    function foo();
}

class SharedClass implements SharedAliasedInterface {    
    function foo(){}
}

class ClassWithAliasAsParameter {

    private $sharedClass;

    function    __construct(SharedClass $sharedClass) {
        $this->sharedClass = $sharedClass;
    }

    function getSharedClass() {
        return $this->sharedClass;
    }
}

class ConcreteClass1 {}

class ConcreteClass2 {}

class ClassWithoutMagicInvoke {}

class TestNoConstructor {}

class TestDependency {
    public $testProp = 'testVal';
}

class TestDependency2 extends TestDependency {
    public $testProp = 'testVal2';
}

class SpecdTestDependency extends TestDependency {
    public $testProp = 'testVal';
}

class TestNeedsDep {
    public function __construct(TestDependency $testDep) {
        $this->testDep = $testDep;
    }
}

class TestClassWithNoCtorTypehints {
    public function __construct($val = 42) {
        $this->test = $val;
    }
}

class TestMultiDepsNeeded {
    public function __construct(TestDependency $val1, TestDependency2 $val2) {
        $this->testDep = $val1;
        $this->testDep = $val2;
    }
}


class TestMultiDepsWithCtor {
    public function __construct(TestDependency $val1, TestNeedsDep $val2) {
        $this->testDep = $val1;
        $this->testDep = $val2;
    }
}

class NoTypehintNullDefaultConstructorClass {
    public $testParam = 1;
    public function __construct(TestDependency $val1, $arg=42) {
        $this->testParam = $arg;
    }
}

class NoTypehintNoDefaultConstructorClass {
    public $testParam = 1;
    public function __construct(TestDependency $val1, $arg = NULL) {
        $this->testParam = $arg;
    }
}

interface DepInterface {}
interface SomeInterface {}
class SomeImplementation implements SomeInterface {}

class DepImplementation implements DepInterface {
    public $testProp = 'something';
}

class RequiresInterface {
    public $dep;
    public function __construct(DepInterface $dep) {
        $this->testDep = $dep;
    }
}

class ClassInnerA {
    public $dep;
    function __construct(ClassInnerB $dep) {$this->dep = $dep;}
}
class ClassInnerB {
    function __construct() {}
}
class ClassOuter {
    public $dep;
    function __construct(ClassInnerA $dep) {$this->dep = $dep;}
}

class ProvTestNoDefinitionNullDefaultClass {
    public function __construct($arg = NULL) {
        $this->arg = $arg;
    }
}

interface TestNoExplicitDefine {}

class ProviderTestCtorParamWithNoTypehintOrDefault implements TestNoExplicitDefine {
    public $val = 42;
    public function __construct($val) {
        $this->val = $val;
    }
}

class ProviderTestCtorParamWithNoTypehintOrDefaultDependent {
    private $param;
    function __construct(TestNoExplicitDefine $param) {
        $this->param = $param;
    }
}

class ProviderTestRawCtorParams {
    public $string;
    public $obj;
    public $int;
    public $array;
    public $float;
    public $bool;
    public $null;
    
    public function __construct($string, $obj, $int, $array, $float, $bool, $null) {
        $this->string = $string;
        $this->obj = $obj;
        $this->int = $int;
        $this->array = $array;
        $this->float = $float;
        $this->bool = $bool;
        $this->null = $null;
    }
}

class ProviderTestParentClass
{
    public function __construct($arg1) {
        $this->arg1 = $arg1;
    }
}

class ProviderTestChildClass extends ProviderTestParentClass
{
    public function __construct($arg1, $arg2) {
        parent::__construct($arg1);
        $this->arg2 = $arg2;
    }

}

class CallableMock {
    function __invoke() {

    }
}

class CallableMockWithArgs {
    function __invoke($arg1, $arg2) {

    }
}

class StringStdClassDelegateMock {
    function __invoke() {
        return $this->make();
    }
    private function make() {
        $obj = new StdClass;
        $obj->test = 42;
        return $obj;
    }
}

class StringDelegateWithNoInvokeMethod {}

class ExecuteClassNoDeps {
    function execute() {
        return 42;
    }
}

class ExecuteClassDeps {
    function __construct(TestDependency $testDep) {}
    function execute() {
        return 42;
    }
}

class ExecuteClassDepsWithMethodDeps {
    function __construct(TestDependency $testDep) {}
    function execute(TestDependency $dep, $arg = NULL) {
        return isset($arg) ? $arg : 42;
    }
}

class ExecuteClassStaticMethod {
    static function execute() {
        return 42;
    }
}

class ExecuteClassRelativeStaticMethod extends ExecuteClassStaticMethod {
    static function execute() {
        return 'this should NEVER be seen since we are testing against parent::execute()';
    }
}

class ExecuteClassInvokable {
    function __invoke() {
        return 42;
    }
}

function testExecuteFunction() {
    return 42;
}

function testExecuteFunctionWithArg(ConcreteClass1 $foo) {
    return 42;
}

class MadeByDelegate {}

class CallableDelegateClassTest {
    function __invoke() {
        return new MadeByDelegate;
    }
}

interface DelegatableInterface {
    function foo();
}

class ImplementsInterface implements DelegatableInterface{
    function foo(){
    }
}

class ImplementsInterfaceFactory{
    public function __invoke() {
        return new ImplementsInterface();
    }
}

class RequiresDelegatedInterface {
    private $interface;

    public function __construct(DelegatableInterface $interface) {
        $this->interface = $interface;
    }
    public function foo() {
        $this->interface->foo();
    }
}

class TestMissingDependency {
 
    function __construct(TypoInTypehint $class) {        
    }
}

class NonConcreteDependencyWithDefaultValue {
    public $interface;
    function __construct(DelegatableInterface $i = NULL) {
        $this->interface = $i;
    }
}


class TypelessParameterDependency {

    public $thumbnailSize;

    function __construct($thumbnailSize) {
        $this->thumbnailSize = $thumbnailSize;
    }
}

class RequiresDependencyWithTypelessParameters {

    public $dependency;

    function __construct(TypelessParameterDependency $dependency) {
        $this->dependency = $dependency;
    }

    function getThumbnailSize() {
        return $this->dependency->thumbnailSize;
    }
}

class HasNonPublicConstructor {
    protected function __construct() {}
}

class HasNonPublicConstructorWithArgs {
    protected function __construct($arg1, $arg2, $arg3) {}
}

class ClassWithCtor {
    function __construct(){}
}

interface BackgroundTask{}

class FetchImageTask implements BackgroundTask {}

class TaskRunner {
    function __construct(BackgroundTask $task, $taskName) {
        $this->task = $task;
    }
}

class FetchImageController {

    public $taskRunner;

    function __construct(TaskRunner $taskRunner) {
        $this->taskRunner = $taskRunner;
    }
}