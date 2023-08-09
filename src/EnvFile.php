<?php

namespace Winter\LaravelConfigWriter;

use Winter\LaravelConfigWriter\Contracts\DataFileInterface;
use Winter\LaravelConfigWriter\Contracts\DataFileLexerInterface;
use Winter\LaravelConfigWriter\Contracts\DataFilePrinterInterface;
use Winter\LaravelConfigWriter\Parser\EnvLexer;
use Winter\LaravelConfigWriter\Printer\EnvPrinter;

/**
 * Class EnvFile
 */
class EnvFile extends DataFile implements DataFileInterface
{
    /**
     * Env file lexer, used to generate ast from src
     *
     * @var DataFileLexerInterface
     */
    protected DataFileLexerInterface $lexer;

    /**
     * Env file printer to convert the ast back to a string
     *
     * @var DataFilePrinterInterface
     */
    protected DataFilePrinterInterface $printer;

    /**
     * Filepath currently being worked on
     */
    protected ?string $filePath = null;

    /**
     * EnvFile constructor
     */
    final public function __construct(
        string $filePath,
        DataFileLexerInterface $lexer = null,
        DataFilePrinterInterface $printer = null
    ) {
        $this->filePath = $filePath;
        $this->lexer = $lexer ?? new EnvLexer();
        $this->printer = $printer ?? new EnvPrinter();
        $this->ast = $this->parse($this->filePath);
    }

    /**
     * Return a new instance of `EnvFile` ready for modification of the file.
     *
     * @return static
     */
    public static function open(string $filePath)
    {
        return new static($filePath);
    }

    /**
     * Set a property within the env. Passing an array as param 1 is also supported.
     *
     * ```php
     * $env->set('APP_PROPERTY', 'example');
     * // or
     * $env->set([
     *     'APP_PROPERTY' => 'example',
     *     'DIF_PROPERTY' => 'example'
     * ]);
     * ```
     * @param string|array<string|int, mixed> $key
     * @param mixed $value
     * @return static
     */
    public function set($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $item => $value) {
                $this->set($item, $value);
            }
            return $this;
        }

        foreach ($this->ast as $index => $item) {
            // Skip all but keys
            if ($item['token'] !== $this->lexer::T_ENV) {
                continue;
            }

            if ($item['value'] === $key) {
                if (
                    !isset($this->ast[$index + 1])
                    || !in_array($this->ast[$index + 1]['token'], [$this->lexer::T_VALUE, $this->lexer::T_QUOTED_VALUE])
                ) {
                    // The next token was not a value, we need to create an empty value node to allow for value setting
                    array_splice($this->ast, $index + 1, 0, [[
                        'token' => $this->lexer::T_VALUE,
                        'value' => ''
                    ]]);
                }

                $this->ast[$index + 1]['value'] = $this->castValue($value);

                // Reprocess the token type to ensure old casting rules are still applied
                switch ($this->ast[$index + 1]['token']) {
                    case $this->lexer::T_VALUE:
                        if (
                            strpos($this->ast[$index + 1]['value'], '"') !== false
                            || strpos($this->ast[$index + 1]['value'], '\'') !== false
                        ) {
                            $this->ast[$index + 1]['token'] = $this->lexer::T_QUOTED_VALUE;
                        }
                        break;
                    case $this->lexer::T_QUOTED_VALUE:
                        if (is_null($value) || $value === true || $value === false) {
                            $this->ast[$index + 1]['token'] = $this->lexer::T_VALUE;
                        }
                        break;
                }

                return $this;
            }
        }

        // We did not find the key in the AST, therefore we must create it
        $this->ast[] = [
            'token' => $this->lexer::T_ENV,
            'value' => $key
        ];
        $this->ast[] = [
            'token' => (is_numeric($value) || is_bool($value)) ? $this->lexer::T_VALUE : $this->lexer::T_QUOTED_VALUE,
            'value' => $this->castValue($value)
        ];

        // Add a new line
        $this->addEmptyLine();

        return $this;
    }

    /**
     * Push a newline onto the end of the env file
     */
    public function addEmptyLine(): EnvFile
    {
        $this->ast[] = [
            'match' => PHP_EOL,
            'token' => $this->lexer::T_WHITESPACE,
        ];

        return $this;
    }

    /**
     * Write the current env lines to a file
     */
    public function write(string $filePath = null): void
    {
        if (!$filePath) {
            $filePath = $this->filePath;
        }

        file_put_contents($filePath, $this->render());
    }

    /**
     * Get the env lines data as a string
     */
    public function render(): string
    {
        return $this->printer->render($this->ast);
    }

    /**
     * Parse a .env file, returns an array of the env file data and a key => position map
     *
     * @return array<int, array<string|int, mixed>>
     */
    protected function parse(string $filePath): array
    {
        if (!is_file($filePath)) {
            return [];
        }

        $contents = file_get_contents($filePath);

        return $this->lexer->parse($contents);
    }

    /**
     * Get the variables from the current env lines data as an associative array
     *
     * @return array<string|int, mixed>
     */
    public function getVariables(): array
    {
        $env = [];

        foreach ($this->ast as $index => $item) {
            if ($item['token'] !== $this->lexer::T_ENV) {
                continue;
            }

            if (!(
                isset($this->ast[$index + 1])
                && in_array($this->ast[$index + 1]['token'], [$this->lexer::T_VALUE, $this->lexer::T_QUOTED_VALUE])
            )) {
                continue;
            }

            $env[$item['value']] = trim($this->ast[$index + 1]['value']);
        }

        return $env;
    }

    /**
     * Cast values to strings
     *
     * @param mixed $value
     */
    protected function castValue($value): string
    {
        if (is_null($value)) {
            return 'null';
        }

        if ($value === true) {
            return 'true';
        }

        if ($value === false) {
            return 'false';
        }

        return str_replace('"', '\"', $value);
    }
}
