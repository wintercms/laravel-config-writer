<?php

namespace Winter\LaravelConfigWriter\Printer;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpParser\ParserAbstract;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\Token;

class ArrayPrinter extends Standard
{
    /**
     * @var ParserAbstract|null Parser for use by `PhpParser`
     */
    protected ?ParserAbstract $parser = null;

    /**
     * Proxy of `prettyPrintFile` to allow for adding lexer token checking support during render.
     * Pretty prints a file of statements (includes the opening <?php tag if it is required).
     *
     * @param Node[] $stmts Array of statements
     *
     * @return string Pretty printed statements
     */
    public function render(array $stmts, ParserAbstract $parser): string
    {
        if (!$stmts) {
            return "<?php\n\n";
        }

        $this->parser = $parser;

        $p = "<?php\n\n" . $this->prettyPrint($stmts);

        if ($stmts[0] instanceof Stmt\InlineHTML) {
            $p = preg_replace('/^<\?php\s+\?>\n?/', '', $p);
        }
        if ($stmts[count($stmts) - 1] instanceof Stmt\InlineHTML) {
            $p = preg_replace('/<\?php$/', '', rtrim($p));
        }

        $this->parser = null;

        return $p;
    }

    /**
     * @param array<int, Node> $nodes
     * @param bool $trailingComma
     * @return string
     */
    protected function pMaybeMultiline(array $nodes, bool $trailingComma = false): string
    {
        if ($this->hasNodeWithComments($nodes) || (isset($nodes[0]) && $nodes[0] instanceof Expr\ArrayItem)) {
            return $this->pCommaSeparatedMultiline($nodes, $trailingComma) . $this->nl;
        } else {
            return $this->pCommaSeparated($nodes);
        }
    }

    /**
     * Pretty prints a comma-separated list of nodes in multiline style, including comments.
     *
     * The result includes a leading newline and one level of indentation (same as pStmts).
     *
     * @param array<int, Node|null> $nodes         Array of Nodes to be printed
     * @param bool   $trailingComma Whether to use a trailing comma
     *
     * @return string Comma separated pretty printed nodes in multiline style
     */
    protected function pCommaSeparatedMultiline(array $nodes, bool $trailingComma): string
    {
        $this->indent();

        $result = '';
        $lastIdx = count($nodes) - 1;
        foreach ($nodes as $idx => $node) {
            if ($node !== null) {
                $comments = $node->getComments();

                if ($comments) {
                    $result .= $this->pComments($comments);
                }

                $result .= $this->nl . $this->p($node);
            } else {
                $result = trim($result) . "\n";
            }
            if ($trailingComma || $idx !== $lastIdx) {
                $result .= ',';
            }
        }

        $this->outdent();
        return $result;
    }

    /**
     * Render an array expression
     *
     * @param Expr\Array_ $node Array expression node
     *
     * @return string Comma separated pretty printed nodes in multiline style
     */
    protected function pExpr_Array(Expr\Array_ $node): string
    {
        $ops = $node->getAttribute('kind', Expr\Array_::KIND_SHORT) === Expr\Array_::KIND_SHORT
            ? ['[', ']']
            : ['array(', ')'];

        if (!count($node->items) && $comments = $this->getNodeComments($node)) {
            // We could previously return the indent string while modifying the indent level, however
            // Now the method is typehinted we cannot, so a little bodge...
            $this->indent();
            $nl = $this->nl;
            $this->outdent();
            // the array has no items, we can inject whatever we want
            return sprintf(
                '%s%s%s%s%s',
                // opening control char
                $ops[0],
                // indent and add nl string
                $nl,
                // join all comments with nl string
                implode($nl, $comments),
                // outdent and add nl string
                $this->nl,
                // closing control char
                $ops[1]
            );
        }

        if ($comments = $this->getCommentsNotInArray($node)) {
            // array has items, we have detected comments not included within the array, therefore we have found
            // trailing comments and must append them to the end of the array
            return sprintf(
                '%s%s%s%s%s%s',
                // opening control char
                $ops[0],
                // render the children
                $this->pMaybeMultiline($node->items, true),
                // add 1 level of indentation
                str_repeat(' ', 4),
                // join all comments with the current indentation
                implode($this->nl . str_repeat(' ', 4), $comments),
                // add a trailing nl
                $this->nl,
                // closing control char
                $ops[1]
            );
        }

        // default return
        return $ops[0] . $this->pMaybeMultiline($node->items, true) . $ops[1];
    }

    /**
     * Get all comments that have not been attributed to a node within a node array
     *
     * @param Expr\Array_ $nodes Array of nodes
     *
     * @return array<int, string> Comments found
     */
    protected function getCommentsNotInArray(Expr\Array_ $nodes): array
    {
        if (!$comments = $this->getNodeComments($nodes)) {
            return [];
        }

        return array_filter($comments, function ($comment) use ($nodes) {
            return !$this->commentInNodeList($nodes->items, $comment);
        });
    }

    /**
     * Recursively check if a comment exists in an array of nodes
     *
     * @param Node[] $nodes Array of nodes
     * @param string $comment The comment to search for
     *
     * @return bool
     */
    protected function commentInNodeList(array $nodes, string $comment): bool
    {
        foreach ($nodes as $node) {
            if (isset($node->value) && $node->value instanceof Expr\Array_ && $this->commentInNodeList($node->value->items, $comment)) {
                return true;
            }
            if ($nodeComments = $node->getAttribute('comments')) {
                foreach ($nodeComments as $nodeComment) {
                    if ($nodeComment->getText() === $comment) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Check the parser tokens for comments within the node's start & end position
     *
     * @param Node $node Node to check
     *
     * @return array<int, string>|null
     */
    protected function getNodeComments(Node $node): ?array
    {
        $tokens = $this->parser->getTokens();
        $pos = $node->getAttribute('startTokenPos');
        $end = $node->getAttribute('endTokenPos');
        $endLine = $node->getAttribute('endLine');
        $comments = [];

        while (++$pos < $end) {
            if (!isset($tokens[$pos]) || (!$tokens[$pos] instanceof Token)) {
                break;
            }

            if ($tokens[$pos]->id === T_WHITESPACE) {
                continue;
            }

            if ($tokens[$pos]->line > $endLine) {
                break;
            }

            // 91 represents the token id for a new array starting, at which point we no longer want to collect
            // the comments from inside, as they do not belong to the node we're looking at, 40 is open paren for old
            // syntax arrays
            if ($tokens[$pos]->id === 91 || $tokens[$pos]->id === T_ARRAY && $tokens[$pos + 1]->id === 40) {
                break;
            }

            if ($tokens[$pos]->id === T_COMMENT || $tokens[$pos]->id === T_DOC_COMMENT) {
                $comments[] = $tokens[$pos]->text;
            } elseif ($comments) {
                break;
            }
        }

        return empty($comments) ? null : $comments;
    }

    /**
     * Prints reformatted text of the passed comments.
     *
     * @param \PhpParser\Comment[] $comments List of comments
     *
     * @return string Reformatted text of comments
     */
    protected function pComments(array $comments): string
    {
        $formattedComments = [];

        foreach ($comments as $comment) {
            $formattedComments[] = str_replace("\n", $this->nl, $comment->getReformattedText());
        }

        $padding = $comments[0]->getStartLine() !== $comments[count($comments) - 1]->getEndLine() ? $this->nl : '';

        // Get the parsed tokens
        $tokens = $this->parser->getTokens();

        // Get the previous and next tokens either side of the comment block
        $previous = $tokens[$comments[array_key_first($comments)]->getStartTokenPos() - 1] ?? null;
        $next = $tokens[$comments[array_key_last($comments)]->getStartTokenPos() + 1] ?? null;

        // If the previous or next node contains duplicate \n then add one additional to the $this->nl, else just nl
        return (($previous->text ?? false) && substr_count($previous->text, PHP_EOL) > 1 ? "\n" : '')
            . $this->nl . trim($padding . implode($this->nl, $formattedComments))
            . (($next->text ?? false) && substr_count($next->text, PHP_EOL) > 1 ? "\n" : '');
    }

    /**
     * @param Expr\Include_ $node
     * @return string
     */
    protected function pExpr_Include(Expr\Include_ $node, int $precedence, int $lhsPrecedence): string
    {
        static $map = [
            Expr\Include_::TYPE_INCLUDE      => 'include',
            Expr\Include_::TYPE_INCLUDE_ONCE => 'include_once',
            Expr\Include_::TYPE_REQUIRE      => 'require',
            Expr\Include_::TYPE_REQUIRE_ONCE => 'require_once',
        ];

        return $map[$node->type] . '(' . $this->p($node->expr) . ')';
    }
}
