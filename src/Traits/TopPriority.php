<?php

declare(strict_types=1);

namespace Estasi\Utility\Traits;

/**
 * Trait TopPriority
 *
 * A trait that is used in classes with a queue to determine the highest priority in a timely manner
 *
 * @package Estasi\Utility\Traits
 */
trait TopPriority
{
    private int $topPriority = 1;

    public function getTopPriority(): int
    {
        return $this->topPriority;
    }

    protected function updateTopPriority(int $priority): void
    {
        if ($priority >= $this->topPriority) {
            $this->topPriority = $priority + 1;
        }
    }
}
