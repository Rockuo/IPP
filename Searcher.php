<?php
/**
 * Created by PhpStorm.
 * User: rockuo
 * Date: 19.2.17
 * Time: 20:06
 */

/**
 * Class Searcher
 * @package IPP
 */
class Searcher
{
    /** @var  Query */
    protected $query;
    /** @var  \DOMElement */
    protected $domXML;

    /**
     * Searcher constructor.
     *
     * @param Query $query
     * @param XML $xml
     */
    public function __construct($query, $xml)
    {
        $this->query = $query;
        $this->domXML = dom_import_simplexml($xml->getXml());
    }

    /**
     * Vybere elementy dle query
     * @return array
     */
    public function searchElements()
    {
        if ($this->query->getLimit() === 0) {
            return [];
        }
        $fromElement = $this->domXML;
        if (!$this->query->isFromIsROOT()) {
            if (!$this->query->getFrom()->compareToDOMElement($fromElement)) {

                /** @var \DOMElement $fromElements */
                $fromElement = $this->getAllFittingElements($this->domXML->childNodes, $this->query->getFrom())[0];
            }
        }
        $possibleSelects = $this->getAllFittingElements($fromElement->childNodes, $this->query->getWhat());

        if ($this->query->hasCondition()) {
            $selects = [];
            /** @var \DOMElement $select */
            foreach ($possibleSelects as $select) {
                if ($this->isSelectVerified($select)) {
                    $selects[] = $select;
                }
            }
        } else {
            $selects = $possibleSelects;
        }
        if ($this->query->hasLimit()) {
            return array_slice($selects, 0, $this->query->getLimit());
        }
        return $selects;
    }

    /**
     * @param \DOMElement $select
     *
     * @return bool
     */
    private function isSelectVerified($select)
    {
        if ($this->query->getCondition()->evaluate($select)) {
            return true;
        } else {
            foreach ($select->childNodes as $element) {
                if ($element instanceof \DOMElement && $this->isSelectVerified($element)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param \DOMNodeList $elements
     *
     * @param ElementOrAttribute $needle
     *
     * @return array
     */
    private function getAllFittingElements(
        $elements,
        $needle
    ) {
        $fromElements = [];
        foreach ($elements as $element) {
            if ($element instanceof \DOMElement) {
                if ($needle->compareToDOMElement($element)) {
                    $fromElements[] = $element;
                } else {
                    $fromElements = array_merge(
                        $fromElements,
                        $this->getAllFittingElements($element->childNodes, $needle)
                    );
                }
            }
        }
        return $fromElements;
    }
}
