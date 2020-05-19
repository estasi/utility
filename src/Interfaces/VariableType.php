<?php

declare(strict_types=1);

namespace Estasi\Utility\Interfaces;

/**
 * Interface VariableType
 *
 * @package Estasi\Utility\Interfaces
 */
interface VariableType
{
    public const BOOLEAN      = 'boolean';
    public const INTEGER      = 'integer';
    public const DOUBLE       = 'double';
    public const STRING       = 'string';
    public const ARRAY        = 'array';
    public const OBJECT       = 'object';
    public const RESOURCE     = 'resource';
    public const NULL         = 'NULL';
    public const UNKNOWN_TYPE = 'unknown type';
}
