<?php

declare(strict_types=1);

namespace Estasi\Utility\Traits;

use OutOfBoundsException;

/**
 * Trait Properties__get
 *
 * @package Estasi\Utility\Traits
 */
trait Properties__get
{
    /**
     * Returns the value of a class property
     * If there is no class property, the \OutOfBoundsException exception is thrown
     *
     * @param string $name
     *
     * @return mixed
     * @throws \OutOfBoundsException
     */
    public function __get($name)
    {
        if ($this->properties->hasKey($name)) {
            return $this->properties->get($name);
        }

        throw new OutOfBoundsException(
            sprintf('The "%s" property of the class "%s" is undefined!', $name, static::class)
        );
    }
}
