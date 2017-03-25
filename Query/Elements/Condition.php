<?php
/**
 * Created by PhpStorm.
 * User: rockuo
 * Date: 14.2.17
 * Time: 22:30
 */

/**
 * Class Condition
 * condition neboli podmínka je součást query,
 * její hlavními vlastnostmi je to že se umí vyhodnotit nad DomElementem
 * @package IPP\Query
 */
class Condition
{
    /** @var bool */
    protected $negation = false; // je podmínka negována?
    /** @var  ElementOrAttribute */
    protected $elementOrAttr;
    /** @var  string */
    protected $operator;
    /** @var  int|float|string */
    protected $literal;

    /**
     * @param bool $negation
     */
    public function setNegation($negation)
    {
        $this->negation = $negation;
    }

    /**
     * @param ElementOrAttribute $elementOrAttr
     */
    public function setElementOrAttr($elementOrAttr)
    {
        $this->elementOrAttr = $elementOrAttr;
    }

    /**
     * @param mixed $literal
     */
    public function setLiteral($literal)
    {
        $this->literal = $literal;
    }

    /**
     * @param string $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return ElementOrAttribute
     */
    public function getElementOrAttr()
    {
        return $this->elementOrAttr;
    }

    /**
     * @return mixed
     */
    public function getLiteral()
    {
        return $this->literal;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @return bool
     */
    public function isNegation()
    {
        return $this->negation;
    }

    public function toggleNegation() // změň negaci => negace = true pokud je počet negací lichý
    {
        $this->negation = !$this->negation;
    }

    /**
     * Vyhodnotí podmínku nad DOMElementem
     *
     * @param \DOMElement $element
     *
     * @return bool
     * @throws QueryException
     */
    public function evaluate($element)
    {
        $evaluation = $this->innerEvaluate($element);
        return $this->negation?!$evaluation:$evaluation;
    }

    /**
     * Vnitří vyhodnocení odmínky, jenž může být následně negováno na základě this->negate
     * @param \DOMElement $element
     *
     * @return bool
     * @throws QueryException
     */
    private function innerEvaluate($element)
    {
        if (!$this->elementOrAttr->compareToDOMElement($element)) {
            return false;
        }

        if ($this->elementOrAttr->getType() === ElementOrAttribute::ELEMENT) {
            if ($element->childNodes->length > 1) {
                return false;
            }
            $value = $this->getValue($element);
        } else {
            $value = $this->getAttributeValue($element);
        }

        switch ($this->operator) {
            case 'CONTAINS':
                if (!is_string($this->literal)) {
                    throw new QueryException();
                }
                return strpos($value, $this->literal);
            case '=':
                return
                    $value === $this->literal ||
                    !is_string($this->literal) ? (float)$value === (float)$this->literal : false;
            case '<':
                return !is_string($value) && !is_string($this->literal) && $value < $this->literal;
            case '>':
                return !is_string($value) && !is_string($this->literal) && $value > $this->literal;
        }
        return false;
    }

    /**
     * @param \DOMElement $element
     *
     * @return float|int|string
     */
    private function getValue($element)
    {
        return $this->parseVal($element->textContent);
    }

    /**
     * @param \DOMElement $element
     *
     * @return float|int|string
     */
    private function getAttributeValue($element)
    {
        return $this->parseVal($element->getAttribute($this->elementOrAttr->getAttribute()));
    }

    /**
     * @param $value
     *
     * @return float|int
     */
    private function parseVal($value)
    {
        if (is_numeric($value)) {
            return Lex::parseNum($value);
        } else {
            return $value;
        }
    }
}
