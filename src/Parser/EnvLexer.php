<?php

namespace Winter\LaravelConfigWriter\Parser;

use Winter\LaravelConfigWriter\Contracts\DataFileLexerInterface;
use Winter\LaravelConfigWriter\Exceptions\EnvParserException;

class EnvLexer implements DataFileLexerInterface
{
    public const T_ENV = 'T_ENV';
    public const T_QUOTED_ENV = 'T_QUOTED_ENV';
    public const T_ENV_NO_VALUE = 'T_ENV_NO_VALUE';
    public const T_WHITESPACE = 'T_WHITESPACE';
    public const T_COMMENT = 'T_COMMENT';

    protected $tokenMap = [
        '/^([\w]*)="?(.+)["|$]/'      => self::T_QUOTED_ENV,
        '/^([\w]*)="?(.*)(?:"|$)/'    => self::T_ENV,
        '/^(\w+)/'                    => self::T_ENV_NO_VALUE,
        '/^(\s+)/'                    => self::T_WHITESPACE,
        '/(#[\w\s]).*/'               => self::T_COMMENT,
    ];

    public function parse(array $src): array
    {
        $tokens = [];

        foreach ($src as $line => $str) {
            $read = 0;
            do {
                $result = $this->match($str, $line, $read);

                if (is_null($result)) {
                    throw new EnvParserException("Unable to parse line " . ($line + 1) . ".");
                }

                $tokens[] = $result;

                $read += strlen($result['match']);
            } while ($read < strlen($str));
        }

        return $tokens;
    }

    public function match(string $str, int $line, int $offset): ?array
    {
        $str = substr($str, $offset);

        foreach ($this->tokenMap as $pattern => $name) {
            if (!preg_match($pattern, $str, $matches)) {
                continue;
            }

            switch ($name) {
                case static::T_ENV:
                    return [
                        'match' => $matches[0],
                        'env' => [
                            'key' => $matches[1],
                            'value' => trim($matches[2]),
                        ],
                        'token' => $name,
                        'line' => $line + 1
                    ];
                case static::T_QUOTED_ENV:
                    return [
                        'match' => $matches[0],
                        'env' => [
                            'key' => $matches[1],
                            'value' => $matches[2],
                        ],
                        'token' => $name,
                        'line' => $line + 1
                    ];
                case static::T_ENV_NO_VALUE:
                    return [
                        'match' => $matches[0],
                        'env' => [
                            'key' => $matches[1],
                            'value' => '',
                        ],
                        'token' => $name,
                        'line' => $line + 1
                    ];
                case static::T_COMMENT:
                    return [
                        'match' => $matches[0],
                        'token' => $name,
                        'line' => $line + 1
                    ];
                case static::T_WHITESPACE:
                    return [
                        'match' => $matches[1],
                        'token' => $name,
                        'line' => $line + 1
                    ];
            }
        }

        return null;
    }
}
