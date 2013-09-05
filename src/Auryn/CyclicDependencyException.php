<?php

namespace Auryn;

/**
 * A catch-all exception for DIC instantiation errors
 */
class CyclicDependencyException extends \RuntimeException {}
