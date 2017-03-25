<?php
/**
 * Created by PhpStorm.
 * User: rockuo
 * Date: 19.2.17
 * Time: 19:26
 */

/**
 * Class XML
 * @package IPP
 */
class XML
{
    /** @var  string */
    protected $xmlString;
    protected $xml;
    protected $array;

    /**
     * ParseXML constructor.
     *
     * @param string $xml
     */
    public function __construct($xml)
    {
        $this->xmlString = $xml;
    }

    public function parse()
    {
        try {
            $this->xml = new \SimpleXMLElement($this->xmlString);
        } catch (\Exception $e) {
            die(4);
        }
    }

    /**
     * @return mixed
     */
    public function getArray()
    {
        return $this->array;
    }

    /**
     * @return mixed
     */
    public function getXml()
    {
        return $this->xml;
    }
}
