<?php

declare(strict_types=1);

namespace Estasi\Utility\Traits;

/**
 * Trait Flags
 *
 * @package Estasi\Utility\Traits
 */
trait Flags
{
    private int $flags;

    private function setFlags(int $flags): void
    {
        $this->flags = $flags;
    }

    private function is(int $flag): bool
    {
        return ($this->flags & $flag) === $flag;
    }
}
