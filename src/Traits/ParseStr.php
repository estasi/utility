<?php

declare(strict_types=1);

namespace Estasi\Utility\Traits;

use Ds\Map;

use function array_combine;
use function htmlspecialchars_decode;
use function preg_match_all;

/**
 * Trait ParseStr
 *
 * @package Estasi\Utility\Traits
 */
trait ParseStr
{
    /**
     * Parses the string into variables
     *
     * @param string $str
     *
     * @return iterable|Map
     */
    private function parseStr(string $str): iterable
    {
        preg_match_all('`(?<=^|\x26)([^\x3D]+)\x3D(.*?)(?=\x26|$)`', htmlspecialchars_decode($str), $matches);

        return new Map(array_combine($matches[1], $matches[2]));
    }
}
