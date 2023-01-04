<?php

namespace Winter\LaravelConfigWriter\Contracts;

interface DataFileLexerInterface
{
    public const T_ENV = 'T_ENV';
    public const T_QUOTED_ENV = 'T_QUOTED_ENV';
    public const T_ENV_NO_VALUE = 'T_ENV_NO_VALUE';
    public const T_WHITESPACE = 'T_WHITESPACE';
    public const T_COMMENT = 'T_COMMENT';

    /**
     * Get the ast from array of src lines
     *
     * @param array<int, string> $src
     * @return array<int, array>
     */
    public function parse(array $src): array;
}
