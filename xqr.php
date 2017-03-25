<?php

/**
 * Created by PhpStorm.
 * User: rockuo
 * Date: 14.2.17
 * Time: 11:04
 */
include_once 'Main.php';
include_once 'Searcher.php';
include_once 'XML.php';
include_once 'Query/QueryParser.php';
include_once 'Query/Elements/Condition.php';
include_once 'Query/Elements/ElementOrAttribute.php';
include_once 'Query/Elements/Keywords.php';
include_once 'Query/Elements/TokenType.php';
include_once 'Query/Elements/Token.php';
include_once 'Query/Elements/Query.php';
include_once 'Query/Parser/Lex.php';
include_once 'Query/Parser/Table.php';
include_once 'Query/Parser/Parser.php';
include_once 'Exceptions/ElementException.php';
include_once 'Exceptions/QueryException.php';
include_once 'Exceptions/NoQueryException.php';



$query = '';
//$query = 'SELECT author FROM ROOT';
$xml = "";
$output = "";
$noHead = false;
$root = "";
foreach ($argv as $arg) {
    $e = explode("=", $arg);
    switch ($e[0]) {
        case '--help':
            if (count($argv) > 2) {
                error_log("no help");
                die(1);
            }
            help();
            return;
            break;
        case '--input':
        case '-i':
            if ($xml) {
                error_log("to many inputs");
                die(1);
            }
            $xml = input($e[1]);
        case '--output':
            $output = $e[1];
            break;
        case '--query':
        case '-q':
            if ($query) {
                error_log("Wrong combination of parameters -qf and -q");
                die(1);
            }
            if (count($e) > 2) {
                $query = "";
                for ($i = 1; $i < count($e); $i++) {
                    $query .= " = " . $e[$i];
                }
            } else {
                $query = $e[1];
            }
            break;
        case '--qf':
            if ($query) {
                error_log("Wrong combination of parameters -qf and -q");
                die(1);
            }
            $query = queryFile($e[1]);
            break;
        case '-n':
            $noHead = true;
            break;
        case '--root':
            $root = $e[1];
            break;
        default:
            break;
    }
}
if (!$query || $query == 'NoFile') {
    error_log("Missing SQL");
    die(80);
} elseif (!$xml) {
    $xml = file_get_contents('php://stdin');
}

try {
    Main::run($query, $xml, $output, $root, $noHead);
} catch (\Exception $e) {
    if ($e instanceof NoQueryException) {
        error_log("Missing SQL");
        die(80);
    } elseif ($e instanceof QueryException) {
        error_log("Wrong SQL");
        die(80);
    } elseif ($e instanceof ElementException) {
        die(80);
    } else {
        die(1);
    }
}


function help()
{
    global $argv;
    echo "------------------------------------------------------------------------------
$argv[0]
===
--help                 - Show help
--input=filename.ext   - Input file with xml
--output=filename.ext  - Output file with xml
--query='query'        - Query under xml - can not be used with -qf attribute
--qf=filename.ext      - Filename with query under xml
-n                     - Xml will be generated without XML header
-r=element             - Name of root element
------------------------------------------------------------------------------
";
}

/**
 * @param $filename
 *
 * @return string
 */
function input($filename)
{
    $xml = file_get_contents($filename, FILE_USE_INCLUDE_PATH);
    if ($xml !== false) {
        return $xml;
    } else {
        die(2);
    }
}


/**
 * @param $filename
 *
 * @return string
 */
function queryFile($filename)
{
    try {
        $queryStr = file_get_contents($filename, FILE_USE_INCLUDE_PATH);
        if ($queryStr) {
            return $queryStr;
        } else {
            return "NoFile";
        }
    } catch (\Exception $e) {
        return "NoFile";
    }
}
