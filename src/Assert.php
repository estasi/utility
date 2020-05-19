<?php

declare(strict_types=1);

namespace Estasi\Utility;

use RuntimeException;

use function extension_loaded;
use function sprintf;

/**
 * Class Assert
 *
 * @package Estasi\Utility
 */
abstract class Assert
{
    /**
     * Throws \RuntimeException an exception if the specified extension is not enabled
     *
     * @param string      $extension php extension name compatible with \extension_loaded
     * @param string|null $message
     */
    public static function extensionLoaded(string $extension, string $message = null)
    {
        if (false === extension_loaded($extension)) {
            throw new RuntimeException(sprintf($message ?? 'PHP extension "%s" not loaded!', $extension));
        }
    }
}
