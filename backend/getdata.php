<?php

const ROUTES_FILE = './data/routes.txt';
const TRIPS_FILE = './data/trips.txt';
const STOP_TIME_FILE = './data/stop_times.txt';
const STOPS_FILE = './data/stops.txt';

function getData(string $file, string $filter):array {
    $csv = shell_exec("head -n 1 $file && grep '$filter' $file");
        
    $array = str_getcsv($csv, "\n");
        
    $mapArray = [];
    
    foreach($array as $Row) $mapArray[] = str_getcsv($Row, ',');
        
    $header = array_shift($mapArray);
        
    array_walk($mapArray, '_combine_array', $header);
    
    return $mapArray;
}

function _combine_array(&$row, $key, $header) {
    $row = array_combine($header, $row);
}
