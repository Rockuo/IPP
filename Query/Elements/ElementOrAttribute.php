<?php
/**
 * Created by PhpStorm.
 * User: rockuo
 * Date: 14.2.17
 * Time: 22:32
 */

/**
 * Class ElementOrAttribute
 * Objekt zjednodušující práci s Elementy a Atributy v query, jeho hlavní sožkou je že může
 * obsahovat jedno druhé i kombinaci, a umí se porovnat s DOMElementem
 * @package IPP\Query
 */
class ElementOrAttribute
{
    const ELEMENT = 'element';
    const ATTRIBUTE = 'attribute';
    const ELEMENT_WITH_ATTRIBUTE = 'element.attribute';
    /** @var  string */
    protected $element = '';
    /** @var  string */
    protected $attribute = '';
    /** @var  string */
    protected $type;

    /**
     * ElementOrAttribute constructor.
     * Rozhodne se, je-li dané "slovo" atribut, element nebo kombinace
     * a tuto informaci si uloží společně s hodnotou/hodnotami
     *
     * @param $word
     *
     * @throws ElementException
     */
    public function __construct($word)
    {
        if (strrpos($word, '"') !== false) {
            throw new ElementException('Not valid element or attribute');
        }
        if ($word[0] === '.') {
            $this->attribute = str_replace('.', '', $word);
            $this->type = self::ATTRIBUTE;
        } elseif (strrpos($word, '.') !== false) {
            $wordParts = explode('.', $word);
            $this->element = $wordParts[0];
            $this->attribute = $wordParts[1];
            $this->type = self::ELEMENT_WITH_ATTRIBUTE;
        } else {
            $this->element = $word;
            $this->type = self::ELEMENT;
        }
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @return string
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     *
     * Porovná objekt s DOMElementem
     * @param \DOMElement $element
     *
     * @return bool
     * @throws ElementException
     */
    public function compareToDOMElement($element)
    {
        $elementOk = $this->element ? $element->tagName === $this->element : false;
        $attrOk = $this->attribute ? !!$element->getAttribute($this->attribute): false;

        switch ($this->type) {
            case self::ATTRIBUTE:
                return $attrOk;
            case self::ELEMENT:
                return $elementOk;
            case self::ELEMENT_WITH_ATTRIBUTE:
                return $elementOk && $attrOk;
            default:
                throw new ElementException();
        }
    }
}
