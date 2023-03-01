<?php

namespace Winter\LaravelConfigWriter\Parser;

use Winter\LaravelConfigWriter\Contracts\DataFileLexerInterface;
use Winter\LaravelConfigWriter\Exceptions\EnvParserException;

class EnvLexer implements DataFileLexerInterface
{
    protected $tokenMap = [
        '/^(\s+)/'                           => self::T_WHITESPACE,
        '/^(#.*)/'                           => self::T_COMMENT,
        '/^(\w+)/s'                          => self::T_ENV,
        '/^="([^"\\\]*(?:\\\.[^"\\\]*)*)"/s' => self::T_QUOTED_VALUE,
        '/^\=(.*)/'                          => self::T_VALUE,
    ];

    /**
     * Parses an array of lines into an AST
     *
     * @param string $string
     * @return array|array[]
     * @throws EnvParserException
     */
    public function parse(string $string): array
    {
        $tokens = [];
        $offset = 0;
        do {
            $result = $this->match($string, $offset);

            if (is_null($result)) {
                throw new EnvParserException("Unable to parse file, failed at: " . $offset . ".");
            }

            $tokens[] = $result;

            $offset += strlen($result['match']);
        } while ($offset < strlen($string));

        return $tokens;
    }

    /**
     * Parse a string against our token map and return a node
     *
     * @param string $str
     * @param int $offset
     * @return array|null
     */
    public function match(string $str, int $offset): ?array
    {
        $source = $str;
        $str = substr($str, $offset);

        foreach ($this->tokenMap as $pattern => $name) {
            if (!preg_match($pattern, $str, $matches)) {
                continue;
            }

            switch ($name) {
                case static::T_ENV:
                case static::T_VALUE:
                case static::T_QUOTED_VALUE:
                    return [
                        'match' => $matches[0],
                        'value' => $matches[1] ?? '',
                        'token' => $name,
                    ];
                case static::T_COMMENT:
                    return [
                        'match' => $matches[0],
                        'token' => $name,
                    ];
                case static::T_WHITESPACE:
                    return [
                        'match' => $matches[1],
                        'token' => $name,
                    ];
            }
        }

        return null;
    }
}
