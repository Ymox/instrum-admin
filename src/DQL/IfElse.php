<?php
namespace App\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

class IfElse extends FunctionNode
{
    private $expr = array();

    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);                // IF
        $parser->match(Lexer::T_OPEN_PARENTHESIS);          // (
        $this->expr[] = $parser->ConditionalExpression();   // condition
        $parser->match(Lexer::T_COMMA);                     //,
        if ($parser->getLexer()->token['type'] == Lexer::T_COUNT) {
            $this->expr[] = $parser->AggregateExpression(); // count(…), sum(…)…
        } else {
            $this->expr[] = $parser->ArithmeticExpression(); //
        }
        $parser->match(Lexer::T_COMMA);
        $this->expr[] = $parser->ArithmeticExpression();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf(
            'IF(%s, %s, %s)',
            $sqlWalker->walkConditionalExpression($this->expr[0]),
            ($this->expr[1] instanceof \Doctrine\ORM\Query\AST\AggregateExpression)
                ? $sqlWalker->walkAggregateExpression($this->expr[1])
                : $sqlWalker->walkArithmeticExpression($this->expr[1]),
            $sqlWalker->walkArithmeticPrimary($this->expr[2])
        );
    }
}