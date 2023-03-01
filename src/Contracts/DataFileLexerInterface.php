<?php

namespace Winter\LaravelConfigWriter\Contracts;

interface DataFileLexerInterface
{
    public const T_ENV = 'T_ENV';
    public const T_VALUE = 'T_VALUE';
    public const T_QUOTED_VALUE = 'T_QUOTED_VALUE';
    public const T_WHITESPACE = 'T_WHITESPACE';
    public const T_COMMENT = 'T_COMMENT';

    /**
     * Get the ast from array of src lines
     *
     * @param string $string
     * @return array<int, array>
     */
    public function parse(string $string): array;
}
