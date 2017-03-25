<?php
/**
 * Created by PhpStorm.
 * User: rockuo
 * Date: 14.2.17
 * Time: 17:15
 */

/**
 * Class Query
 * Třída obsahující nastavení Query, v podstatě je všechny jen obalí
 * obsahuje metodu na určení správného nastavení
 * @package IPP\Query
 */
class Query
{
    /** @var  ElementOrAttribute */
    protected $what;
    /** @var  ElementOrAttribute */
    protected $from;
    /** @var bool */
    protected $fromIsROOT = false;
    /** @var  Condition */
    protected $condition = null;
    /** @var  int|float */
    protected $limit = null;

    /**
     * @return bool
     */
    public function hasCondition()
    {
        return !!$this->condition;
    }

    /**
     * @return bool
     */
    public function hasLimit()
    {
        return $this->limit !== null;
    }

    /**
     * @param Condition $condition
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;
    }

    /**
     * @param ElementOrAttribute $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @param ElementOrAttribute $what
     */
    public function setWhat($what)
    {
        $this->what = $what;
    }

    /**
     * @return Condition
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @return ElementOrAttribute
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return ElementOrAttribute
     */
    public function getWhat()
    {
        return $this->what;
    }

    /**
     * @param bool $fromIsROOT
     */
    public function setFromIsROOT($fromIsROOT)
    {
        $this->fromIsROOT = $fromIsROOT;
    }

    /**
     * @return bool
     */
    public function isFromIsROOT()
    {
        return $this->fromIsROOT;
    }

    public function check()
    {
        if ($this->what === null) {
            throw  new QueryException();
        }

        if ($this->from === null && !$this->fromIsROOT) {
            throw  new QueryException();
        }
    }
}
