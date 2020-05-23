<?php

declare(strict_types=1);

namespace Estasi\Utility\Traits;

/**
 * Trait GetPatternsHtml5AndPcre
 *
 * @package Estasi\Utility\Traits
 */
trait GetPatternsHtml5AndPcre
{
    /**
     * Returns an array with PCRE and HTML5 patterns
     *
     * @param string $pattern
     *
     * @return array<string, string>
     */
    private function getPatternsHtml5AndPCRE(string $pattern): array
    {
        if (preg_match('`^([^A-Za-z0-9\x5C\s])(.+?)\1[imsxADSUXJu]*?$`', preg_quote($pattern), $match)) {
            return ['pcre' => $pattern, 'html' => $match[2]];
        }

        return ['pcre' => sprintf('`%s`', $pattern), 'html' => $pattern];
    }
}
