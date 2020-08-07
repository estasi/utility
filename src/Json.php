<?php

declare(strict_types=1);

namespace Estasi\Utility;

use function json_decode;
use function json_encode;

use const JSON_BIGINT_AS_STRING;
use const JSON_INVALID_UTF8_IGNORE;
use const JSON_NUMERIC_CHECK;
use const JSON_PARTIAL_OUTPUT_ON_ERROR;
use const JSON_PRESERVE_ZERO_FRACTION;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_UNICODE;

/**
 * Class Json
 *
 * @package Estasi\Utility
 */
abstract class Json
{
    public const DEFAULT_OPTIONS_ENCODE = JSON_THROW_ON_ERROR | JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE
                                          | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION;
    public const DEFAULT_OPTIONS_DECODE = JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING | JSON_INVALID_UTF8_IGNORE;
    
    /**
     * Returns json data converted to an associative array
     *
     * @param string $json    The json string being decoded. This function only works with UTF-8 encoded strings.
     * @param int    $options [optional] Bit mask of constants JSON_BIGINT_AS_STRING, JSON_INVALID_UTF8_IGNORE,
     *                        JSON_INVALID_UTF8_SUBSTITUTE, JSON_OBJECT_AS_ARRAY, JSON_THROW_ON_ERROR.
     *                        By default, the following constants are already defined: JSON_BIGINT_AS_STRING,
     *                        JSON_INVALID_UTF8_IGNORE, JSON_THROW_ON_ERROR
     * @param int    $depth   [optional] User specified recursion depth.
     *
     * @return array
     * @throws \JsonException
     * @noinspection PhpUndefinedClassInspection
     * @noinspection PhpDocRedundantThrowsInspection
     */
    public static function decode(string $json, int $options = self::DEFAULT_OPTIONS_DECODE, int $depth = 512): array
    {
        return json_decode($json, true, $depth, $options);
    }
    
    /**
     * Returns json data converted to an object
     *
     * @param string $json    The json string being decoded. This function only works with UTF-8 encoded strings.
     * @param int    $options [optional] Bit mask of constants JSON_BIGINT_AS_STRING, JSON_INVALID_UTF8_IGNORE,
     *                        JSON_INVALID_UTF8_SUBSTITUTE, JSON_OBJECT_AS_ARRAY, JSON_THROW_ON_ERROR.
     *                        By default, the following constants are already defined: JSON_BIGINT_AS_STRING,
     *                        JSON_INVALID_UTF8_IGNORE, JSON_THROW_ON_ERROR
     * @param int    $depth   [optional] User specified recursion depth.
     *
     * @return object
     */
    public static function decodeToObject(
        string $json,
        int $options = self::DEFAULT_OPTIONS_DECODE,
        int $depth = 512
    ): object {
        return json_decode($json, false, $depth, $options);
    }
    
    /**
     * Returns the JSON representation of a value
     *
     * @param mixed $value   <p>The <i>value</i> being encoded. Can be any type except a resource.</p>
     * @param int   $options [optional] Bitmask consisting.
     *                       By default, the following constants are already defined: JSON_THROW_ON_ERROR,
     *                       JSON_NUMERIC_CHECK, JSON_UNESCAPED_UNICODE, JSON_PARTIAL_OUTPUT_ON_ERROR,
     *                       JSON_PRESERVE_ZERO_FRACTION
     * @param int   $depth   [optional] User specified recursion depth.
     *
     * @return string
     * @throws \JsonException
     * @noinspection PhpUndefinedClassInspection
     * @noinspection PhpDocRedundantThrowsInspection
     */
    public static function encode($value, int $options = self::DEFAULT_OPTIONS_ENCODE, int $depth = 512): string
    {
        return json_encode($value, $options, $depth);
    }
}
