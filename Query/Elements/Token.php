<?php
/**
 * Created by PhpStorm.
 * User: rockuo
 * Date: 14.2.17
 * Time: 17:48
 */

/**
 * Class Token
 * token, přenosový Objekt pro komunikaci mezi Lexikálním a Syntaktickým analizátorem
 * @package IPP\Query
 */
class Token
{
    /** @var  int */
    protected $type;
    protected $value;

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @param int $type
     *
     * @return bool
     */
    public function typeIs($type)
    {
        return $this->type === $type;
    }
}
