<?php
/**
 * Created by PhpStorm.
 * User: rockuo
 * Date: 14.2.17
 * Time: 23:19
 */

/**
 * Class QueryParser
 * Volá parsování query, není to podstatná část, je zde z "historických" důvodů, jelikož
 * při psaní kodu bylo předpokládáno že tato část bude složitější
 * @package IPP\Query
 */
class QueryParser
{
    /**
     * @param $string
     *
     * @return Query
     */
    public static function parseQuery($string)
    {
        return (new Parser((new Lex($string))->parseQuery()))->parse();
    }
}
