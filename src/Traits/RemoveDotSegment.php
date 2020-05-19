<?php

declare(strict_types=1);

namespace Estasi\Utility\Traits;

use function array_pop;
use function count;
use function end;
use function explode;
use function implode;
use function strcmp;
use function strncmp;
use function strstr;
use function substr;

/**
 * Trait PathDotSegment
 *
 * @package Estasi\Utility\Traits
 */
trait RemoveDotSegment
{
    /**
     * Remove Dot Segments
     *
     * The algorithm is based on the recommendations of section 5.2.4 of RFC-3986
     *
     * @see  https://tools.ietf.org/html/rfc3986#section-5.2.4
     * @link https://tools.ietf.org/html/rfc3986#section-5.2.4
     *
     * @param string|null $path
     *
     * @return string|null
     */
    private function removeDotSegment(?string $path): ?string
    {
        $isBufferConsistsOnlyDots = fn(string $str): bool => (!strcmp($str, '..') || !strcmp($str, '.'));
        // RFC 3986 5.2.4 2D
        if (empty($path) || $isBufferConsistsOnlyDots($path)) {
            return null;
        }
        // RFC 3986 5.2.4 2A
        if (0 === strncmp($path, '.', 1)) {
            $path = substr(strstr($path, '/'), 1);
        }
        $input  = explode('/', $path);
        $output = [];
        foreach ($input as $segment) {
            if (0 === strcmp($segment, '..')) {
                // RFC 3986 5.2.4 2C
                array_pop($output);
                if (count($output) === 0) {
                    $output[] = '';
                }
            } elseif (0 !== strcmp($segment, '.')) {
                // RFC 3986 5.2.4 2E
                $output[] = $segment;
            }
        }
        // according to RFC 3986 5.2.4 2B and 2C add "/"
        if ($isBufferConsistsOnlyDots(end($input))) {
            $output[] = '';
        }

        return implode('/', $output);
    }
}
