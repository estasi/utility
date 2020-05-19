<?php

declare(strict_types=1);

namespace Estasi\Utility\Traits;

use OverflowException;

use function sprintf;

/**
 * Trait Disable__set
 *
 * Disables the use of the __set magic method in the current class and in child classes
 *
 * @package Estasi\Utility\Traits
 */
trait Disable__set
{
    final public function __set($name, $value)
    {
        throw new OverflowException(
            sprintf('You cannot assign a value to the "%s" parameter of the class "%s"!', $name, static::class)
        );
    }
}
