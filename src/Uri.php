<?php

declare(strict_types=1);

namespace Estasi\Utility;

use Ds\Map;
use Estasi\Utility\Interfaces\VariableType;
use InvalidArgumentException;
use OutOfBoundsException;

use function array_combine;
use function array_diff;
use function array_fill_keys;
use function array_flip;
use function array_intersect_key;
use function array_merge;
use function array_pad;
use function boolval;
use function explode;
use function gettype;
use function in_array;
use function is_null;
use function is_string;
use function mb_strpos;
use function preg_match;
use function sprintf;
use function str_replace;
use function strcasecmp;
use function strncmp;
use function strpos;
use function strrpos;
use function substr;

/**
 * Class Uri
 *
 * @package Estasi\Uri
 */
final class Uri implements Interfaces\Uri
{
    use Traits\Disable__set;
    use Traits\Disable__call;
    use Traits\Disable__callStatic;
    use Traits\RemoveDotSegment;
    use Traits\ParseStr;
    use Traits\ReceivedTypeForException;

    private Map $uri;

    /**
     * @inheritDoc
     */
    public function __construct($uri = self::WITHOUT_URI, bool $ignorePassword = self::IGNORE_PASSWORD)
    {
        switch (gettype($uri)) {
            /** @noinspection PhpMissingBreakStatementInspection */
            case VariableType::NULL:
                $uri = '';
            case VariableType::STRING:
                $this->parseUri($uri, $ignorePassword);
                break;
            /** @noinspection PhpMissingBreakStatementInspection */
            case VariableType::OBJECT:
                if ($uri instanceof Interfaces\Uri) {
                    $this->uri = new Map($this->unsetPass($uri->toArray(), $ignorePassword));
                    break;
                }
            default:
                throw new InvalidArgumentException(
                    sprintf(
                        'To create an object, the uri parameter was expected as a string, NULL or an object implementing the %s interface; %s was received!',
                        Interfaces\Uri::class,
                        $this->getReceivedType($uri)
                    )
                );
        }
    }

    /**
     * @param string $name
     *
     * @return string|null
     * @throws \OutOfBoundsException
     */
    public function __get($name)
    {
        switch ($name) {
            case self::USERINFO:
                return ($user = $this->uri->get(self::USER))
                    ? $user . (($pass = $this->uri->get(self::PASS)) ? ':' . $pass : '')
                    : null;
            case self::AUTHORITY:
                return $this->toString($name) ?: null;
            default:
                if ($this->uri->hasKey($name)) {
                    return $this->uri->get($name);
                }
                throw new OutOfBoundsException(
                    sprintf('The requested parameter "%s" is not part of the URI structure!', $name)
                );
        }
    }

    /**
     * The base Uri is the current object
     *
     * @inheritDoc
     */
    public function merge($uri): Interfaces\Uri
    {
        $r = new self($uri);
        if ($r->scheme) {
            $r->uri->put(self::PATH, $this->removeDotSegment($r->path));

            return $r;
        }
        if ($r->authority) {
            $r->uri->putAll([self::SCHEME => $this->scheme, self::PATH => $this->removeDotSegment($r->path)]);

            return $r;
        }

        $t = new self();
        if ($r->path) {
            $basePath = (string)$this->path;
            $path     = 0 !== strncmp($r->path, '/', 1)
                ? ($this->authority && empty($basePath) ? '/' : substr($basePath, 0, strrpos($basePath, '/') + 1))
                : '';
            $path     = $this->removeDotSegment($path . $r->path);
            $query    = $r->query;
        } else {
            $path  = $this->path;
            $query = $r->query ?? $this->query;
        }
        $t->uri->putAll(
            $this->uri->diff(new Map(array_flip([self::PATH, self::QUERY, self::FRAGMENT])))
                      ->merge([self::PATH => $path, self::QUERY => $query, self::FRAGMENT => $r->fragment])
        );

        return $t;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->uri->toArray();
    }

    /**
     * @inheritDoc
     */
    public function toString(string ...$parts): string
    {
        $parts = $this->uri->intersect($this->getPartsUri($parts))
                           ->filter(fn(string $key, ?string $value) => false === is_null($value));
        $uri   = '';
        foreach ($parts as $part => $value) {
            switch ($part) {
                case self::SCHEME:
                    $uri .= $value . ':';
                    continue 2;
                case self::HOST:
                    $uri .= '//';
                    // userinfo
                    $uri .= $parts->hasKey(self::USER)
                        ? $parts->get(self::USER) . ($parts->hasKey(self::PASS) ? ':' . $parts->get(self::PASS) : '')
                          . '@'
                        : '';
                    // host
                    $uri .= $value;
                    // port
                    $uri .= $parts->hasKey(self::PORT) ? ':' . $parts->get(self::PORT) : '';
                    continue 2;
                case self::PATH:
                    $uri .= $parts->get(self::PATH);
                    continue 2;
                case self::QUERY:
                    $uri .= '?' . $parts->get(self::QUERY);
                    continue 2;
                case self::FRAGMENT:
                    $uri .= '#' . $parts->get(self::FRAGMENT);
                    continue 2;
            }
        }

        return $uri;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @inheritDoc
     */
    public function isAbsoluteUri(): bool
    {
        return $this->scheme && !$this->fragment;
    }

    /**
     * @inheritDoc
     */
    public function isNetworkPath(): bool
    {
        return !$this->scheme && $this->host;
    }

    /**
     * @inheritDoc
     */
    public function isAbsolutePath(): bool
    {
        return false === $this->isNetworkPath() && 0 === strncmp($this->path, '/', 1);
    }

    /**
     * @inheritDoc
     */
    public function isRelativePath(): bool
    {
        return false === $this->isAbsolutePath();
    }

    /**
     * @inheritDoc
     */
    public function isRelativeReference(): bool
    {
        return $this->isNetworkPath() || $this->isAbsolutePath() || $this->isRelativePath() || $this->query
               || $this->fragment;
    }

    /**
     * @inheritDoc
     */
    public function isEmail(): bool
    {
        return 0 === strcasecmp((string)$this->scheme, self::SCHEME_MAILTO) || mb_strpos((string)$this->path, '@');
    }

    /**
     * @inheritDoc
     */
    public function queryAsArray(): array
    {
        return $this->parseStr((string)$this->query)
                    ->toArray();
    }

    /**
     * @inheritDoc
     */
    public function with(string $part, ?string $value): Interfaces\Uri
    {
        $new = clone $this;
        switch ($part) {
            case self::AUTHORITY:
                $new->uri->putAll($new->parseAuthority($value));
                break;
            default:
                if ($new->uri->hasKey($part)) {
                    $new->uri->put($part, $value);
                }
        }

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function without(string ...$parts): Interfaces\Uri
    {
        if (in_array(self::HOST, $parts)) {
            $parts = array_merge($parts, [self::USER, self::PASS, self::PORT]);
        }

        return $this->withAll(array_fill_keys($parts, null));
    }

    /**
     * @inheritDoc
     */
    public function withAll(iterable $parts): Interfaces\Uri
    {
        $parts = (new Map($parts))->filter(
            fn($key, $value): bool => is_string($key) && (is_string($value) || is_null($value))
        );
        if ($parts->hasKey(self::AUTHORITY)) {
            $parts->putAll($this->parseAuthority($parts->get(self::AUTHORITY)));
        }

        $new = clone $this;
        $new->uri->putAll($parts->intersect($new->uri));

        return $new;
    }

    public function __clone()
    {
        $this->uri = $this->uri->copy();
    }

    /**
     * Parsing a URI Reference with a Regular Expression
     * The algorithm adapted according to Appendix B. of RFC-3986
     *
     * @link https://tools.ietf.org/html/rfc3986#appendix-B
     *
     * @param string $uri
     * @param bool   $ignorePassword
     */
    private function parseUri(string $uri, bool $ignorePassword): void
    {
        // Parsing a uri string into parts
        preg_match(
            '`^((?P<scheme>[^:/?#]+):)?(//(?P<host>[^/?#]*))?(?P<path>[^?#]*)(\?(?P<query>[^#]*))?(#(?P<fragment>.*))?`Su',
            str_replace('\\', '/', $uri),
            $match
        );

        $this->uri = new Map(
            array_fill_keys(
                [self::SCHEME, self::USER, self::PASS, self::HOST, self::PORT, self::PATH, self::QUERY, self::FRAGMENT],
                null
            )
        );
        $this->uri->putAll(
            (new Map($match))->merge($this->parseAuthority($match[self::HOST], $ignorePassword))
                             ->filter(fn($key, $value): bool => is_string($key) && boolval($value))
        );
    }

    private function parseAuthority(?string $authority, bool $ignorePassword = true): array
    {
        if ($authority) {
            $userinfo = [];
            // to separate the userinfo from host
            $posCommercialAt = strpos($authority, '@');
            if (false !== $posCommercialAt) {
                $userinfo  = array_combine(
                    [self::USER, self::PASS],
                    array_pad(explode(':', substr($authority, 0, $posCommercialAt)), 2, null)
                );
                $userinfo  = $this->unsetPass($userinfo, $ignorePassword);
                $authority = substr($authority, $posCommercialAt + 1);
            }
            preg_match(
                '`^(?P<host>\x5B[[:xdigit:]\x3A]+\x5D|[^\x3A\x2F\x3F\x23\x40]+)(\x3A(?P<port>\d+))?$`Su',
                $authority,
                $matches
            );

            return array_intersect_key(
                array_merge($userinfo, $matches),
                array_flip([self::USER, self::PASS, self::HOST, self::PORT])
            );
        }

        return array_fill_keys([self::USER, self::PASS, self::HOST, self::PORT], null);
    }

    /**
     * @param array $uri
     *
     * @param bool  $ignorePassword
     *
     * @return array
     */
    private function unsetPass(array $uri, bool $ignorePassword): array
    {
        if ($ignorePassword && boolval($uri[self::PASS])) {
            $uri[self::PASS] = null;
        }

        return $uri;
    }

    /**
     * @param array $parts
     *
     * @return \Ds\Map
     */
    private function getPartsUri(array $parts): Map
    {
        if (empty($parts)) {
            $parts = [
                self::SCHEME,
                self::USER,
                self::PASS,
                self::HOST,
                self::PORT,
                self::PATH,
                self::QUERY,
                self::FRAGMENT,
            ];
            goto _return_;
        }

        if (in_array(self::AUTHORITY, $parts)) {
            $parts = array_merge($parts, [self::USER, self::PASS, self::HOST, self::PORT]);
            goto _return_;
        }

        if (false === in_array(self::HOST, $parts)) {
            $parts = array_diff($parts, [self::USER, self::PASS, self::PORT]);
            goto _return_;
        }

        if (false === in_array(self::USER, $parts)) {
            $parts = array_diff($parts, [self::PASS]);
        }

        if (in_array(self::USERINFO, $parts)) {
            $parts = array_merge($parts, [self::USER, self::PASS]);
        }

        _return_:

        return new Map(array_flip($parts));
    }
}
