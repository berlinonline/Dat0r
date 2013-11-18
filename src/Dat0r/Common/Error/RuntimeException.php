<?php

namespace Dat0r\Common\Error;

use Dat0r\IException;
use RuntimeException as SplRuntimeException;

/**
 * Reflects logic errors during runtime.
 * For example non-executed (switch)cases or unexpected state transitions.
 */
class RuntimeException extends SplRuntimeException implements IException
{
}
