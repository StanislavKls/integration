<?php

require_once('./vendor/crest-master/src/crest.php');

print_r($_REQUEST);
$data = json_decode(file_get_contents('php://input'), true);
writeToLog($data, 'incoming');
writeToLog($_REQUEST, 'incoming');
writeToLog($_POST, 'incoming');

/**
 * Write data to log file.
 *
 * @param mixed $data
 * @param string $title
 *
 * @return bool
 */
function writeToLog($data, $title = '') {
    $log = "\n------------------------\n";
    $log .= date("Y.m.d G:i:s") . "\n";
    $log .= (strlen($title) > 0 ? $title : 'DEBUG') . "\n";
    $log .= print_r($data, 1);
    $log .= "\n------------------------\n";
    file_put_contents(getcwd() . '/norder.log', $log, FILE_APPEND);
    return true;
} 
