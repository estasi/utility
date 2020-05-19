<?php

declare(strict_types=1);

namespace Estasi\Utility\Traits;

use function preg_match;
use function preg_quote;
use function sprintf;

/**
 * Trait ConvertPatternHtml5ToPCRE
 *
 * @package Estasi\Utility\Traits
 */
trait ConvertPatternHtml5ToPCRE
{
    private function convertPatternHtml5ToPCRE(string $pattern): string
    {
        if (preg_match('`^([^A-Za-z0-9\x5C\s]).+?\1[imsxADSUXJu]*?$`', preg_quote($pattern))) {
            return $pattern;
        }

        return sprintf('`%s`', $pattern);
    }
}
