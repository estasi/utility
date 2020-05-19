<?php

declare(strict_types=1);

namespace Estasi\Utility\Interfaces;

/**
 * Interface Uri
 *
 * @property-read string|null $scheme
 * @property-read string|null $user
 * @property-read string|null $pass
 * @property-read string|null $host
 * @property-read string|null $port
 * @property-read string|null $path
 * @property-read string|null $query
 * @property-read string|null $fragment
 * @property-read string|null $authority
 * @property-read string|null $userinfo
 * @package Estasi\Uri\Interfaces
 */
interface Uri
{
    // the names of the constructor parameters
    public const OPT_URI             = 'uri';
    public const OPT_IGNORE_PASSWORD = 'ignorePassword';
    // the values of the parameters of the constructor by default
    public const WITHOUT_URI          = null;
    public const IGNORE_PASSWORD      = true;
    public const DONT_IGNORE_PASSWORD = false;
    // uri parts
    public const SCHEME   = 'scheme';
    public const USER     = 'user';
    public const PASS     = 'pass';
    public const HOST     = 'host';
    public const PORT     = 'port';
    public const PATH     = 'path';
    public const QUERY    = 'query';
    public const FRAGMENT = 'fragment';
    // object parts of the uri
    public const AUTHORITY = 'authority';
    public const USERINFO  = 'userinfo';
    // list of main uri schemas
    public const SCHEME_FTP     = 'ftp';
    public const SCHEME_FTPS    = 'ftps';
    public const SCHEME_SFTP    = 'sftp';
    public const SCHEME_HTTP    = 'http';
    public const SCHEME_HTTPS   = 'https';
    public const SCHEME_MAILTO  = 'mailto';
    public const SCHEME_FILE    = 'file';
    public const SCHEME_DATA    = 'data';
    public const SCHEME_TEL     = 'tel';
    public const SCHEME_GOPHER  = 'gopher';
    public const SCHEME_NNTP    = 'nntp';
    public const SCHEME_NEWS    = 'news';
    public const SCHEME_TELNET  = 'telnet';
    public const SCHEME_TN_3270 = 'tn3270';
    public const SCHEME_IMAP    = 'imap';
    public const SCHEME_POP     = 'pop';
    public const SCHEME_LDAP    = 'ldap';
    // list of default ports of the main schemas
    public const SCHEME_DEFAULTS_PORTS = [
        self::SCHEME_HTTP    => 80,
        self::SCHEME_HTTPS   => 443,
        self::SCHEME_FTP     => 21,
        self::SCHEME_SFTP    => 115,
        self::SCHEME_FTPS    => 990,
        self::SCHEME_GOPHER  => 70,
        self::SCHEME_NNTP    => 119,
        self::SCHEME_NEWS    => 119,
        self::SCHEME_TELNET  => 23,
        self::SCHEME_TN_3270 => 23,
        self::SCHEME_IMAP    => 143,
        self::SCHEME_POP     => 110,
        self::SCHEME_LDAP    => 389,
    ];


    /** @var string pct-encoded Characters: "%" HEXDIG HEXDIG */
    public const PCT_ENCODED_RFC3986 = '\x25[[:xdigit:]]{2}';
    /** @var string gen-delims Characters: ":" | "/" | "?" | "#" | "[" | "]" | "@" */
    public const GEN_DELIMS_RFC3986 = '\x3A\x2F\x3F\x23\x5B\x5D\x40';
    /** @var string sub-delims Characters: "!" | "$" | "&" | "'" | "(" | ")" | "*" | "+" | "," | ";" | "=" */
    public const SUB_DELIMS_RFC3986 = '\x21\x24\x26\x27\x28\x29\x2A\x2B\x2C\x3B\x3D';
    /** @var string gen-delims | sub-delims */
    public const RESERVED_RFC3986 = self::GEN_DELIMS_RFC3986 . self::SUB_DELIMS_RFC3986;
    /** @var string Unreserved Characters: ALPHA | DIGIT | "-" | "." | "_" | "~" */
    public const UNRESERVED_RFC3986 = '[:alpha:][:digit:]\x2D\x2E\x5F\x7E';

    /**
     * Returns the result of binding all keys of a given passable object or uri string with their corresponding
     * values in combination with the current instance.
     *
     * This method MUST be implemented in such a way that the Uri object remains unchanged!
     *
     * The algorithm MUST be adapted according to section 5.2.2, 5.2.3 and 5.2.4 of RFC-3986
     *
     * @link https://tools.ietf.org/html/rfc3986#section-5.2.2
     * @link https://tools.ietf.org/html/rfc3986#section-5.2.3
     * @link https://tools.ietf.org/html/rfc3986#section-5.2.4
     *
     * @param string|\Estasi\Utility\Interfaces\Uri $uri
     *
     * @return static new instance
     * @api
     */
    public function merge($uri): self;

    /**
     * Returns an associative array containing parts of the parsed uri string
     *
     * @return array<string, string|null>
     * @api
     */
    public function toArray(): array;

    /**
     * Returns a uri string with the specified parts
     * If parts of the uri are omitted, the maximum complete string is returned
     * Returns an empty string if the uri is invalid and cannot be created
     *
     * The algorithm MUST be adapted according to section 5.3 of RFC-3986
     *
     * @link https://tools.ietf.org/html/rfc3986#section-5.3
     *
     * @param string ...$parts
     *
     * @return string
     * @api
     */
    public function toString(string ...$parts): string;

    /**
     * Returns a uri string
     * Returns an empty string if the uri is invalid and cannot be created
     *
     * @return string
     */
    public function __toString();

    /**
     * Check if the URI is an absolute URI
     *
     * The algorithm MUST be adapted according to section 4.3 of RFC-3986
     *
     * @link https://tools.ietf.org/html/rfc3986#section-4.3
     *
     * @return bool
     * @api
     */
    public function isAbsoluteUri(): bool;

    /**
     * Returns true if the relative reference starts with a single slash character, otherwise returns false
     * The algorithm MUST be adapted according to section 4.2 of RFC-3986
     *
     * @link https://tools.ietf.org/html/rfc3986#section-4.2
     *
     * @return bool
     * @api
     */
    public function isAbsolutePath(): bool;

    /**
     * Returns true if the relative reference does not start with a slash character, otherwise returns false
     * The algorithm MUST be adapted according to section 4.2 of RFC-3986
     *
     * @link https://tools.ietf.org/html/rfc3986#section-4.2
     *
     * @return bool
     * @api
     */
    public function isRelativePath(): bool;

    /**
     * Returns true if the relative reference starts with two slashes, otherwise returns false
     * The algorithm MUST be adapted according to section 4.2 of RFC-3986
     *
     * @link https://tools.ietf.org/html/rfc3986#section-4.2
     *
     * @return bool
     * @api
     */
    public function isNetworkPath(): bool;

    /**
     * Check if the URI is a valid relative URI
     *
     * The algorithm MUST be adapted according to section 4.2 of RFC-3986
     *
     * @link https://tools.ietf.org/html/rfc3986#section-4.2
     *
     * @return bool
     * @api
     */
    public function isRelativeReference(): bool;

    /**
     * Check if the URI is a valid email address
     *
     * @return bool
     */
    public function isEmail(): bool;

    /**
     * Return the query string as an associative array of key => value pairs
     *
     * @return array<string, mixed>
     * @api
     */
    public function queryAsArray(): array;

    /**
     * Returns a Uri object with the new value of the uri part
     *
     * This method MUST be implemented in such a way that the Uri object remains unchanged!
     *
     * This method MUST accept the following parts of the uri:
     * SCHEME | AUTHORITY (USER | PASS | HOST | PORT) | PATH | QUERY | FRAGMENT
     *
     * @param string      $part
     * @param string|null $value
     *
     * @return static new instance
     * @api
     */
    public function with(string $part, ?string $value): self;

    /**
     * Returns a Uri object with new values for parts of the uri
     *
     * This method MUST be implemented in such a way that the Uri object remains unchanged!
     *
     * This method MUST accept the following parts of the uri:
     * SCHEME | AUTHORITY (USER | PASS | HOST | PORT) | PATH | QUERY | FRAGMENT
     *
     * @param iterable<string, string|null> $parts
     *
     * @return static
     * @api
     */
    public function withAll(iterable $parts): self;

    /**
     * Returns a new Uri object with deleted parts of the uri
     *
     * This method MUST be implemented in such a way that the Uri object remains unchanged!
     *
     * This method MUST accept the following parts of the uri:
     * SCHEME | AUTHORITY (USER | PASS | HOST | PORT) | PATH | QUERY | FRAGMENT
     *
     * @param string ...$parts
     *
     * @return static new instance
     * @api
     */
    public function without(string ...$parts): self;

    /**
     * Uri constructor.
     *
     * @link https://tools.ietf.org/html/rfc3986#section-3.2.1
     *
     * @param string|\Estasi\Utility\Interfaces\Uri|null $uri
     * @param bool                                       $ignorePassword ignoring the Password string as recommended in
     *                                                                   section 3.2.1 of RFC 3986
     */
    public function __construct($uri = self::WITHOUT_URI, bool $ignorePassword = self::IGNORE_PASSWORD);

}
