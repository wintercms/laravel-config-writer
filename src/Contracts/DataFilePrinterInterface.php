<?php

namespace Winter\LaravelConfigWriter\Contracts;

interface DataFilePrinterInterface
{
    /**
     * Transform the ast back to a src file string
     * @param array<int, array> $ast
     * @return string
     */
    public function render(array $ast): string;
}
