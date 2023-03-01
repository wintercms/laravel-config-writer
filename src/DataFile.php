<?php

namespace Winter\LaravelConfigWriter;

use Winter\LaravelConfigWriter\Contracts\DataFileInterface;

abstract class DataFile implements DataFileInterface
{
    /**
     * Abstract syntax tree
     *
     * @var \PhpParser\Node\Stmt[]|array<int, array>
     */
    protected $ast = [];

    /**
     * Get currently loaded AST
     *
     * @return \PhpParser\Node\Stmt[]|array|null
     */
    public function getAst()
    {
        return $this->ast;
    }
}
