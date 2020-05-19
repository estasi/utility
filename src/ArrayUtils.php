<?php

declare(strict_types=1);

namespace Estasi\Utility;

use Generator;

use function array_shift;
use function boolval;
use function explode;
use function is_iterable;
use function is_object;
use function method_exists;

/**
 * Class ArrayUtils
 *
 * @package Estasi\Utility
 */
abstract class ArrayUtils
{
    /**
     * Returns the found value in the iterated object for the specified key.
     *
     * The search can be performed by unlimited nesting, in which case the nesting keys must be separated by the symbol
     * "."
     *
     * @param string     $key      Search key
     * @param iterable   $haystack Iterable
     * @param mixed|null $default  Default value if the search failed
     *
     * @return mixed|null
     */
    public static function get(string $key, iterable $haystack, $default = null)
    {
        $haystack = self::iteratorToArray($haystack);
        if (empty($key) || empty($haystack)) {
            return $default;
        }

        if (isset($haystack[$key])) {
            return $haystack[$key];
        }

        $find = function (array $keys, array $haystack) use (&$find) {
            $key = array_shift($keys);
            if (false === isset($haystack[$key])) {
                return null;
            }

            return boolval($keys) ? $find($keys, $haystack[$key]) : $haystack[$key];
        };

        return $find(explode('.', $key), $haystack) ?? $default;
    }

    /**
     * If the pseudo type variable iterable is an object that implements Traversable, the function copies the iterator
     * to an array
     *
     * @param iterable $data
     * @param bool     $useKeys Whether to use the iterator element keys as index.
     *
     * @return array
     */
    public static function iteratorToArray(iterable $data, bool $useKeys = true): array
    {
        if (is_object($data) && method_exists($data, 'toArray')) {
            return $data->toArray();
        }

        $generator = function (iterable $data) use ($useKeys): Generator {
            $i = 0;
            foreach ($data as $key => $value) {
                $value = is_iterable($value) ? self::iteratorToArray($value, $useKeys) : $value;
                $key   = $useKeys ? $key : $i++;
                yield $key => $value;
            }
        };

        $array = [];
        foreach ($generator($data) as $key => $value) {
            $array[$key] = $value;
        }

        return $array;
    }
}
