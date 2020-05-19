<?php

declare(strict_types=1);

namespace Estasi\Utility;

use InvalidArgumentException;
use RuntimeException;

use function class_exists;
use function get_class;
use function gettype;
use function is_null;
use function is_object;
use function is_string;
use function sprintf;

/**
 * Class UriFactory
 *
 * @package Estasi\Utility
 */
abstract class UriFactory
{
    public const WITHOUT_URI         = null;
    public const DEFAULT_URI_HANDLER = Uri::class;

    /**
     * Returns a uri handler object
     *
     * @param string|\Estasi\Utility\Interfaces\Uri|null $uri
     * @param string|\Estasi\Utility\Interfaces\Uri      $uriHandler
     *
     * @return \Estasi\Utility\Interfaces\Uri
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public static function make($uri = self::WITHOUT_URI, $uriHandler = self::DEFAULT_URI_HANDLER): Interfaces\Uri
    {
        if (is_string($uriHandler)) {
            if (false === class_exists($uriHandler)) {
                throw new RuntimeException(sprintf('Class %s not found! Unable to create an object!', $uriHandler));
            }
            $uriHandler = new $uriHandler();
        }
        if (false === $uriHandler instanceof Interfaces\Uri) {
            throw new InvalidArgumentException(
                sprintf(
                    'The URI handler class must implement the %s interface; received %s!',
                    Interfaces\Uri::class,
                    self::getType($uriHandler)
                )
            );
        }

        if (is_null($uri)) {
            return $uriHandler;
        }
        if (false === (is_string($uri) || $uri instanceof Interfaces\Uri)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Uri expected a string, null or object implementing the %s interface; received %s!',
                    Interfaces\Uri::class,
                    self::getType($uri)
                )
            );
        }

        return $uriHandler->merge($uri);
    }

    /**
     * @param mixed $var
     *
     * @return string
     */
    private static function getType($var): string
    {
        return is_object($var) ? get_class($var) : gettype($var);
    }
}
