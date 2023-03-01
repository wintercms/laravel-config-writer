<?php

namespace Winter\LaravelConfigWriter\Printer;

use Winter\LaravelConfigWriter\Contracts\DataFilePrinterInterface;
use Winter\LaravelConfigWriter\Parser\EnvLexer;

class EnvPrinter implements DataFilePrinterInterface
{
    public function render(array $ast): string
    {
        $output = '';

        foreach ($ast as $item) {
            switch ($item['token']) {
                case EnvLexer::T_ENV:
                    $output .= $item['value'];
                    break;
                case EnvLexer::T_VALUE:
                    $output .= sprintf('=%s', $item['value']);
                    break;
                case EnvLexer::T_QUOTED_VALUE:
                    $output .= sprintf('="%s"', $item['value']);
                    break;
                case EnvLexer::T_COMMENT:
                case EnvLexer::T_WHITESPACE:
                    $output .= $item['match'];
                    break;
            }
        }

        return $output;
    }
}
