<?php

namespace Winter\LaravelConfigWriter\Contracts;

interface DataFileLexerInterface
{
    /**
     * Get the ast from array of src lines
     */
    public function parse(array $src): array;
}
