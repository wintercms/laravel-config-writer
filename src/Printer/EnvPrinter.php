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
                    $output .= sprintf('%s=%s', $item['env']['key'], $item['env']['value']);
                    break;
                case EnvLexer::T_QUOTED_ENV:
                    $output .= sprintf('%s="%s"', $item['env']['key'], $item['env']['value']);
                    break;
                case EnvLexer::T_ENV_NO_VALUE:
                    $output .= $item['env']['key'];
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
