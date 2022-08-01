<?php

namespace Winter\LaravelConfigWriter\Contracts;

interface DataFileInterface
{
    /**
     * Return a new instance of `DataFileInterface` ready for modification of the provided filepath.
     *
     * @return static
     */
    public static function open(string $filePath);

    /**
     * Set a property within the data.
     *
     * @param string|array<string|int, mixed> $key
     * @param mixed $value
     * @return static
     */
    public function set($key, $value = null);

    /**
     * Write the current data to a file
     */
    public function write(?string $filePath = null): void;

    /**
     * Get the printed data
     */
    public function render(): string;
}
