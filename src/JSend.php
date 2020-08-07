<?php

declare(strict_types=1);

namespace Estasi\Utility;

use Ds\Map;
use JsonSerializable;
use OutOfBoundsException;
use OutOfRangeException;

use function array_merge;
use function compact;
use function json_encode;
use function sprintf;
use function strcmp;
use function strtolower;
use function substr;
use function substr_compare;

use const JSON_BIGINT_AS_STRING;
use const JSON_NUMERIC_CHECK;
use const JSON_PARTIAL_OUTPUT_ON_ERROR;
use const JSON_PRESERVE_ZERO_FRACTION;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_UNICODE;

/**
 * Class JSend
 *
 * @method string getStatus()
 * @method bool isSuccess()
 * @method bool isFail()
 * @method bool isError()
 * @method mixed getData()
 * @method string|null getMessage()
 * @method int|null getCode()
 * @package Estasi\Utility
 */
final class JSend implements JsonSerializable
{
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAIL    = 'fail';
    public const STATUS_ERROR   = 'error';
    
    private Map $response;
    
    /**
     * JSend constructor.
     *
     * @param string      $status
     * @param mixed|null  $data    Acts as the wrapper for any data returned by the API call. If the call returns no
     *                             data
     *                             (as in the last example), data should be set to null.
     * @param string|null $message Again, provides the wrapper for the details of why the request failed. If the
     *                             reasons for failure correspond to POST values, the response object's keys SHOULD
     *                             correspond to those POST values.
     * @param int|null    $code    A numeric code corresponding to the error, if applicable
     *
     * @throws \OutOfRangeException
     */
    public function __construct(string $status, $data = null, ?string $message = null, ?int $code = null)
    {
        $this->response = new Map();
        switch ($status) {
            case self::STATUS_SUCCESS:
            case self::STATUS_FAIL:
                $this->response->putAll(compact('status', 'data'));
                break;
            case self::STATUS_ERROR:
                $this->response->putAll(compact('status', 'message'));
                if (isset($data)) {
                    $this->response->put('data', $data);
                }
                if (isset($code)) {
                    $this->response->put('code', $data);
                }
                break;
            default:
                throw new OutOfRangeException('Unknown JSend status; expected: success, fail or error!');
        }
    }
    
    /**
     * @param mixed|null $data Acts as the wrapper for any data returned by the API call. If the call returns no data
     *                         (as in the last example), data should be set to null.
     *
     * @return static
     */
    public static function success($data = null): self
    {
        return new self(self::STATUS_SUCCESS, $data);
    }
    
    /**
     * @param mixed|null $data Provides the wrapper for the details of why the request failed. If the reasons for
     *                         failure correspond to POST values, the response object's keys SHOULD correspond to those
     *                         POST values.
     *
     * @return static
     */
    public static function fail($data = null): self
    {
        return new self(self::STATUS_FAIL, $data);
    }
    
    /**
     * @param string     $message  Again, provides the wrapper for the details of why the request failed. If the
     *                             reasons for failure correspond to POST values, the response object's keys SHOULD
     *                             correspond to those POST values.
     * @param int|null   $code     A numeric code corresponding to the error, if applicable
     * @param mixed|null $data     A generic container for any other information about the error, i.e. the conditions
     *                             that caused the error, stack traces, etc.
     *
     * @return static
     */
    public static function error(string $message, ?int $code = null, $data = null): self
    {
        return new self(self::STATUS_ERROR, $data, $message, $code);
    }
    
    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->response->toArray();
    }
    
    public function __call($name, $arguments)
    {
        switch ($name) {
            case 'getStatus':
                return $this->response->get('status');
            case 'isSuccess':
            case 'isFail':
            case 'isError':
                return 0 === substr_compare($name, $this->response->get('status'), 2, null, true);
            case 'getData':
            case 'getMessage':
            case 'getCode':
                return $this->response->get(strtolower(substr($name, 3)), null);
            default:
                throw new OutOfBoundsException(sprintf('Unknown method "%s" is requested!', $name));
        }
    }
    
    /**
     * @throws \JsonException
     * @noinspection PhpUndefinedClassInspection
     */
    public function __toString()
    {
        return json_encode(
            $this,
            JSON_THROW_ON_ERROR | JSON_NUMERIC_CHECK | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION
            | JSON_UNESCAPED_UNICODE | JSON_BIGINT_AS_STRING
        );
    }
    
    /**
     * @param string $token
     * @param string $name
     *
     * @return $this
     */
    public function withNewCsrfToken(string $token, string $name = 'CSRF-Token'): self
    {
        $this->response->apply(
            fn(string $key, $val) => (0 === strcmp($key, 'data')) ? array_merge((array)$val, [$name => $token]) : $val
        );
        
        return $this;
    }
}
