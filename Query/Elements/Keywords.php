<?php
/**
 * Created by PhpStorm.
 * User: rockuo
 * Date: 16.2.17
 * Time: 14:33
 */

/**
 * Class Keywords
 * seznam klíčových slov použitých při Lexikální analýze pro rozpoznání.
 * @package IPP\Query\Elements
 */
class Keywords
{
    const SELECT = 'SELECT';
    const LIMIT = 'LIMIT';
    const ROOT = 'ROOT';
    const WHERE = 'WHERE';
    const NOT = 'NOT';
    const FROM = 'FROM';
    const KEYWORDS = ['SELECT', 'LIMIT', 'WHERE', 'FROM', 'ROOT', 'NOT'];
}
