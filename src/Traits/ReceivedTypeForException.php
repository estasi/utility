<?php

declare(strict_types=1);

namespace Estasi\Utility\Traits;

use function get_class;
use function gettype;
use function is_object;

/**
 * Trait ReceivedTypeForException
 *
 * @package Estasi\Utility\Traits
 */
trait ReceivedTypeForException
{
    /**
     * Returns the data type as a string for an exception
     *
     * @param mixed $var
     *
     * @return string
     */
    protected function getReceivedType($var): string
    {
        return is_object($var) ? get_class($var) : gettype($var);
    }
}
