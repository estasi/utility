<?php

declare(strict_types=1);

namespace Estasi\Utility\Traits;

use BadMethodCallException;

use function sprintf;

/**
 * Trait Disable__call
 *
 * Disables the use of the __call magic method in the current class and in child classes
 *
 * @package Estasi\Utility\Traits
 */
trait Disable__call
{
    final public function __call($name, $arguments)
    {
        throw new BadMethodCallException(
            sprintf('The requested method "%s" is not defined in the class "%s"!', $name, static::class)
        );
    }
}
