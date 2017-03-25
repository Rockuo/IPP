<?php
/**
 * Created by PhpStorm.
 * User: rockuo
 * Date: RULE_EQV.self::RULE_LIMIT_EMPTY.17
 * Time: 23:44
 */

/**
 * Class Table
 * @package IPP\Query\Parser
 */
class Table
{

    const STATE_QUERY = 1;
    const STATE_LIMIT_FULL = 3;
    const STATE_MULTI_FROM = 45;
    const STATE_WHERE_COND = 8;
    const STATE_ORDER_EMPTY = 11;
    const T_EOA = 'EOA';
    const TABLE = [
        self::STATE_QUERY => [
            Keywords::FROM => self::STATE_MULTI_FROM,
        ],
        self::STATE_MULTI_FROM => [
            Keywords::WHERE => self::STATE_WHERE_COND,
            Keywords::LIMIT => self::STATE_LIMIT_FULL
        ],
        self::STATE_WHERE_COND => [
            Keywords::LIMIT => self::STATE_LIMIT_FULL
        ],
    ];
}
