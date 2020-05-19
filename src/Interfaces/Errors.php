<?php

declare(strict_types=1);

namespace Estasi\Utility\Interfaces;

/**
 * Interface Errors
 *
 * @package Estasi\Utility\Interfaces
 */
interface Errors
{
    /**
     * Returns an error confirmation
     *
     * @param string $errorCode
     *
     * @return bool
     * @api
     */
    public function isLastError(string $errorCode): bool;

    /**
     * Returns an error code
     *
     * @return string|null
     * @api
     */
    public function getLastErrorCode(): ?string;

    /**
     * Returns an error message as a string or null if there is no error
     *
     * @return string|null
     * @api
     */
    public function getLastErrorMessage(): ?string;

    /**
     * Returns an array of error messages generated during data validation.
     * The array key is error codes.
     * Array values contain an error message.
     *
     * @return iterable
     * @api
     */
    public function getLastErrors(): iterable;

    /**
     * Returns an array of the last error message that occurred during data validation.
     * The array key is the error code.
     * The array value contains an error message.
     *
     * @return iterable containing code and error message
     * @api
     */
    public function getLastError(): iterable;
}
