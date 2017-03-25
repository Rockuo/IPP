<?php

/**
 * Created by PhpStorm.
 * User: rockuo
 * Date: 14.2.17
 * Time: 11:09
 */

/**
 * Class main
 * Hlavní část souboru
 * @package IPP
 */
class Main
{
    /**
     * @param string $query
     * @param string $input
     * @param string $output
     * @param string $rootName
     * @param bool $noHead
     */
    public static function run($query, $input, $output, $rootName, $noHead)
    {
        /** @var Query $query */
        $query = QueryParser::parseQuery($query);

        /** @var XML $xml */
        $xml = new XML($input);
        $xml->parse();

        $searcher = new Searcher($query, $xml);

        $elements = $searcher->searchElements();
        /** @var \DOMDocument $doc */
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;

        $head = $doc->saveXML();
        if ($rootName) {
            /** @var \DOMElement $root */
            $root = $doc->createElement($rootName);
            $root = $doc->appendChild($root);
        } else {
            /** @var \DOMDocument $root */
            $root = $doc;
        }
        /** @var \DOMElement $element */
        foreach ($elements as $element) {
            $element1 = $doc->importNode($element, true);
            $root->appendChild($element1);
        }
        $strXml = $doc->saveXML();
        if ($noHead) {
            $strXml = str_replace($head, '', $strXml);
        }
        if ($output) {
            file_put_contents($output, $strXml);
        } else {
            echo $strXml;
        }
    }
}
