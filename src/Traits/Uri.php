<?php

declare(strict_types=1);

namespace Estasi\Utility\Traits;

use Estasi\Utility\Interfaces\Uri as UriHandler;

use function preg_match;
use function preg_replace;
use function sprintf;

/**
 * Trait Uri
 *
 * @package Estasi\Utility\Traits
 */
trait Uri
{

    private function isValidScheme(string $scheme): bool
    {
        return (bool)preg_match('`^[[:alpha:]][[:alnum:]\x2b\x2d\x2e]*?`', $scheme);
    }

    private function isHostIPLiteral(string &$host): bool
    {
        /** @noinspection RegExpRedundantEscape */
        if (preg_match('`^\[([^\[x5B]\x5D]+)\]$`', $host, $matches)) {
            $host = $matches[1];

            return true;
        }

        return false;
    }

    private function isIPvFuture(string $ip): bool
    {
        $IPvFuturePattern = sprintf(
            '`^v[[xdigit]]+\x2E[%s%s\x3A]+$`iuS',
            UriHandler::UNRESERVED_RFC3986,
            UriHandler::SUB_DELIMS_RFC3986
        );

        return empty(preg_replace($IPvFuturePattern, '', $ip));
    }

    private function isIPv6Address(string $ip, array &$matches = []): bool
    {
        $h16                = '[[:xdigit:]]{1,4}';
        $IPv6AddressPattern = sprintf(
            '`^(?:
                        (%3$s%3$s%3$s%3$s%3$s%3$s)                  #                            6( h16 ":" ) ls32
                        |\x3A{2}(?:%3$s){5}                         #                       "::" 5( h16 ":" ) ls32
                        |(?:%1$s)?\x3A{2}(?:%3$s){4}                # [               h16 ] "::" 4( h16 ":" ) ls32
                        |(?:(?:%3$s)?%1$s)?\x3A{2}(?:%3$s){3}       # [ *1( h16 ":" ) h16 ] "::" 3( h16 ":" ) ls32
                        |(?:(?:%3$s){0,2}%1$s)?\x3A{2}(?:%3$s){2}   # [ *2( h16 ":" ) h16 ] "::" 2( h16 ":" ) ls32
                        |(?:(?:%3$s){0,3}%1$s)?\x3A{2}%3$s          # [ *3( h16 ":" ) h16 ] "::"    h16 ":"   ls32
                        |(?:(?:%3$s){0,4}%1$s)?\x3A{2}              # [ *4( h16 ":" ) h16 ] "::"              ls32
                    )(%2$s)$
                    |^(?:(?:%3$s){0,5}%1$s)?\x3A{2}%1$s$            # [ *5( h16 ":" ) h16 ] "::"              h16
                    |^(?:(?:%3$s){0,6}%1$s)?\x3A{2}$                # [ *6( h16 ":" ) h16 ] "::"
                    `x',
            $h16,
            sprintf('%1$s\x3A%1$s|%2$s', $h16, $this->getPatternIPv4Address()), // ls32
            $h16 . '\x3A' // h16 ":"
        );

        return (bool)preg_match($IPv6AddressPattern, $ip, $matches);
    }

    private function isHostIPv4Address(string $host): bool
    {
        return (bool)preg_match(sprintf('`^%s$`', $this->getPatternIPv4Address()), $host);
    }

    private function isHostRegName(string $host): bool
    {
        $regNamePattern = sprintf(
            '`[%s%s]|%s`uS',
            UriHandler::UNRESERVED_RFC3986,
            UriHandler::SUB_DELIMS_RFC3986,
            UriHandler::PCT_ENCODED_RFC3986
        );

        return empty(preg_replace($regNamePattern, '', $host));
    }

    private function isPortOutsideOfTheAllowedRange(int $port): bool
    {
        return $port < 1 || $port > 65535;
    }

    private function getPatternIPv4Address(): string
    {
        return sprintf('%1$s.%1$s.%1$s.%1$s', '\d|\x31-\x39\d|1\d{2}|2\x30-\x34\d|25\x30-\35');
    }
}
