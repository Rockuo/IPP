<?php
/**
 * Created by PhpStorm.
 * User: rockuo
 * Date: 14.2.17
 * Time: 17:14
 */

/**
 * Class Parser
 * Syntaktický Analyzátor
 * jeho cílem je "předělat", posloupnost tokenů v Query
 * @package IPP\Query
 */
class Parser
{
    /** @var  array */
    protected $tokens;
    /** @var  array */
    protected $flow;
    /** @var  Query */
    protected $query;

    /**
     * Parser constructor.
     *
     * @param array $tokens
     */
    public function __construct($tokens)
    {
        $this->tokens = $tokens;
        $this->query = new Query();
    }

    /**
     * vytvoří Query z posloupnosti tokenů, pomocí parseTokens, pmocí LL tabulky
     * @return Query
     * @throws QueryException
     */
    public function parse()
    {
        $this->flow = $this->tokens;
        $this->parseTokens();
        if (count($this->flow)) { // pokud není flow prazdné => chyba, nezpracované celé query
            throw new QueryException();
        }
        $this->query->check();
        return $this->query;
    }

    /**
     * @param int|string $state
     *
     * @throws QueryException
     * @internal param int $offset
     */
    private function parseTokens($state = Table::STATE_QUERY)
    {

        $this->processState($state);

        if (!count($this->flow)) {
            return;
        }
        /** @var Token $token */
        $token = $this->flow[0];

        switch ($token->getType()) {
            case TokenType::KEYWORD:
                $key = $token->getValue();
                break;
            default:
                throw new QueryException();
        }
        if (key_exists($state, Table::TABLE)) {
            if (!key_exists($key, Table::TABLE[$state])) {
                throw new QueryException();
            }
            $this->parseTokens(Table::TABLE[$state][$key]);
            return;
        }
    }

    /**
     * @param $state
     *
     * @throws QueryException
     */
    private function processState($state)
    {
        /** @var Token $token */
        $token = $this->nextToken();
        if ($token === null) {
            throw new QueryException();
        }
        switch ($state) {
            case Table::STATE_QUERY:
                $this->checkKeywordToken(Keywords::SELECT, $token);
                $this->processQuery();
                break;
            case Table::STATE_LIMIT_FULL:
                $this->checkKeywordToken(Keywords::LIMIT, $token);
                $this->processLimit();
                break;
            case Table::STATE_MULTI_FROM:
                $this->checkKeywordToken(Keywords::FROM, $token);
                $this->processFrom();
                break;
            case Table::STATE_WHERE_COND:
                $this->checkKeywordToken(Keywords::WHERE, $token);
                $this->processCondition();
                break;
            default:
                throw new QueryException();
        }
    }

    /**
     * Zracuje začátek query
     * @throws QueryException
     */
    private function processQuery()
    {
        $token = $this->nextToken();
        /** @var ElementOrAttribute $tokenValue */
        $tokenValue = $token->getValue();
        if ($token->getType() !== TokenType::ELEMENT_OR_ATTRIBUTE ||
            $tokenValue->getType() !== ElementOrAttribute::ELEMENT
        ) {
            throw new QueryException();
        }
        $this->query->setWhat($tokenValue);
    }

    /**
     * zpracuje Limit
     * @throws QueryException
     */
    private function processLimit()
    {
        $token = $this->nextToken();
        $tokenValue = Lex::parseNum($token->getValue());

        if ($token->getType() !== TokenType::LITERAL || !is_int($tokenValue) || $tokenValue < 0) {
            throw new QueryException();
        }
        $this->query->setLimit($tokenValue);
    }

    /**
     * zpracuje FROM klauzuli
     */
    private function processFrom()
    {
        $token = $this->nextToken();
        if ($token->getType() === TokenType::ELEMENT_OR_ATTRIBUTE) {
            $this->query->setFrom($token->getValue());
        } else {
            $this->checkKeywordToken(Keywords::ROOT, $token);
            $this->query->setFromIsROOT(true);
        }
    }

    /**
     * zpracuje podmínku
     * @throws QueryException
     */
    private function processCondition()
    {
        /** @var Condition $condition */
        $condition = new Condition();
        /** @var Token $token */
        $token = $this->nextToken();

        while ($token->getValue() === Keywords::NOT) {
            $condition->toggleNegation();
            $token = $this->nextToken();
        }

        if ($token->getType() !== TokenType::ELEMENT_OR_ATTRIBUTE) {
            throw new QueryException();
        }
        $condition->setElementOrAttr($token->getValue());

        $token = $this->nextToken();
        if ($token->getType() !== TokenType::OPERATOR) {
            throw new QueryException();
        }
        $condition->setOperator($token->getValue());


        $token = $this->nextToken();
        if ($token->getType() !== TokenType::LITERAL) {
            throw new QueryException();
        }
        $condition->setLiteral($token->getValue());

        $this->query->setCondition($condition);
    }

    /**
     * získání dalšího tokenu
     * @throws QueryException
     */
    private function nextToken()
    {
        $token = array_shift($this->flow);
        if ($token === null) {
            throw new QueryException();
        }
        return $token;
    }

    /**
     *
     * Kontrola, je-li token požadované klíčové slovo
     * @param Token $token
     * @param $keyWord
     *
     * @throws QueryException
     */
    private function checkKeywordToken($keyWord, $token)
    {
        if (!$token->typeIs(TokenType::KEYWORD) || $token->getValue() !== $keyWord) {
            throw new QueryException();
        }
    }
}
