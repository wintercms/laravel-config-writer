<?php

namespace Winter\LaravelConfigWriter\Parser;

/**
 * Used with ArrayFile to inject a function call into a PHP array file
 */
class PHPFunction
{
    /**
     * @var string Function name
     */
    protected $name;

    /**
     * @var array<string|int, mixed> Function arguments
     */
    protected $args;

    /**
     * @param string $name
     * @param array<string|int, mixed> $args
     */
    public function __construct(string $name, array $args = [])
    {
        $this->name = $name;
        $this->args = $args;
    }

    /**
     * Get the function name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the function arguments
     *
     * @return array<string|int, mixed>
     */
    public function getArgs(): array
    {
        return $this->args;
    }
}
