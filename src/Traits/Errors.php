<?php

declare(strict_types=1);

namespace Estasi\Utility\Traits;

use Estasi\Utility\ArrayUtils;

use function array_key_last;
use function array_merge;
use function array_slice;
use function array_unique;
use function end;
use function strcmp;

/**
 * Trait Errors
 *
 * @package Estasi\Utility\Traits
 */
trait Errors
{
    private array $errors = [];

    /**
     * @inheritDoc
     */
    public function isLastError(string $errorCode): bool
    {
        return 0 === strcmp($errorCode, $this->getLastErrorCode());
    }

    /**
     * @inheritDoc
     */
    public function getLastErrorCode(): ?string
    {
        return array_key_last($this->errors);
    }

    /**
     * @inheritDoc
     */
    public function getLastErrorMessage(): ?string
    {
        $lastErrorMessage = end($this->errors);

        return $lastErrorMessage ?: null;
    }

    /**
     * @inheritDoc
     */
    public function getLastErrors(): iterable
    {
        return array_unique($this->errors);
    }

    /**
     * @inheritDoc
     */
    public function getLastError(): iterable
    {
        return array_slice($this->errors, -1);
    }

    /**
     * @param string $code
     * @param string $message
     */
    protected function setError(string $code, string $message): void
    {
        $this->errors[$code] = $message;
    }

    /**
     * Initializes an array of error messages.
     *
     * Attention!!! If the array of error messages already contained the values, then it is overwritten.
     * To complement the array, use mergeErrors()
     *
     * @param iterable $errors
     */
    protected function setErrors(iterable $errors): void
    {
        $this->errors = ArrayUtils::iteratorToArray($errors);
    }

    /**
     * @param iterable $errors
     */
    protected function mergeErrors(iterable $errors): void
    {
        $this->errors = array_merge($this->errors, ArrayUtils::iteratorToArray($errors));
    }
}
