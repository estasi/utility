<?php

declare(strict_types=1);

namespace Estasi\Utility\Traits;

use BadMethodCallException;

use function sprintf;

/**
 * Trait Disable__callStatic
 *
 * Disables the use of the __callStatic magic method in the current class and in child classes
 *
 * @package Estasi\Utility\Traits
 */
trait Disable__callStatic
{
    final public static function __callStatic($name, $arguments)
    {
        throw new BadMethodCallException(
            sprintf('The requested static method "%s" is not defined in the class "%s"!', $name, static::class)
        );
    }
}
