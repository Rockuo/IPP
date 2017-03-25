<?php
/**
 * Created by PhpStorm.
 * User: rockuo
 * Date: 14.2.17
 * Time: 11:06
 */

/**
 * Class Lex
 * Lexikální analyzátor
 * @package IPP
 */
class Lex
{
    protected $queryString;
    protected $tokens = [];
    protected $flow = [];

    /**
     * Lex constructor.
     *
     * @param $string
     */
    public function __construct($string)
    {
        $this->queryString = $string;
    }

    /**
     * Rozparsuje query na pole tokenů.
     * @return array
     * @throws NoQueryException
     */
    public function parseQuery()
    {
        if (!$this->queryString) {
            throw new NoQueryException('No Query is Set');
        }
        $this->flow = str_split($this->queryString);
        while (count($this->flow)) {
            $this->getToken();
        }
        return $this->tokens;
    }

    /**
     * Lokalizuje token, určí jeho typ a hodnotu, přidá token do pole tokenů
     */
    private function getToken()
    {
        $char = array_shift($this->flow);
        if ($char === '"' || $char === '\'') { //je li to string
            $this->tokenString($char);
        } elseif ($char === '<' || $char === '>' || $char === '=') {
            $token = new Token();
            $token->setType(TokenType::OPERATOR);
            $token->setValue($char);
            $this->tokens[] = $token;
        } elseif (is_numeric($char)) { // je li to číslo
            $literal = "";
            $isFirstDot = true;
            while (is_numeric($char) || ($char === '.' && $isFirstDot)) {
                $literal .= $char;
                if ($char === '.') {
                    $isFirstDot = false;
                }
                $char = array_shift($this->flow);
            }
            $token = new Token();
            $token->setType(TokenType::LITERAL);
            $token->setValue(self::parseNum($literal));
            $this->tokens[] = $token;
            array_unshift($this->flow, $char);
        } else {
            $elOrAttrOrKey = "";
            $binThere = false;
            while (!(preg_match('/\s/', $char) ||
                $char === '<' ||
                $char === '>' ||
                $char === '=' ||
                $char === '"' ||
                $char === '\'' ||
                $char === null)
            ) {
                $binThere = true;
                $elOrAttrOrKey .= $char;
                $char = array_shift($this->flow);
            }
            if ($binThere) {
                $this->parseElAttKey($elOrAttrOrKey);
                array_unshift($this->flow, $char);
            }
        }
    }

    /**
     * @param string $word
     */
    private function parseElAttKey($word)
    {
        if (array_search($word, Keywords::KEYWORDS) !== false) {
            $token = new Token();
            $token->setType(TokenType::KEYWORD);
            $token->setValue($word);
        } elseif ($word === 'CONTAINS') {
            $token = new Token();
            $token->setType(TokenType::OPERATOR);
            $token->setValue($word);
        } else {
            $token = new Token();
            $token->setType(TokenType::ELEMENT_OR_ATTRIBUTE);
            $token->setValue(new ElementOrAttribute($word));
        }
        $this->tokens[] = $token;
    }

    /**
     * @param string $quote
     */
    private function tokenString($quote)
    {
        $literal = '';
        $char = array_shift($this->flow);
        while ($char !== $quote) {
            $literal .= $char;
            $char = array_shift($this->flow);
        }
        $token = new Token();
        $token->setType(TokenType::LITERAL);
        $token->setValue($literal);
        $this->tokens[] = $token;
    }

    /**
     * @param string $word
     *
     * @return float|int
     */
    public static function parseNum($word)
    {
        return strpos($word, '.') === false ? (int)$word : (float)$word;
    }
}
