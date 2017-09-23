<?php

const ROUTES_FILE = './data/routes.txt';
const TRIPS_FILE = './data/trips.txt';
const STOP_TIME_FILE = './data/stop_times.txt';
const STOPS_FILE = './data/stops.txt';

function getData(string $file, string $filter = ""):array {
    $header = str_getcsv(shell_exec("head -n 1 $file"), ',');
    if ($filter != "") {
        $csv = shell_exec("grep '$filter' $file");
    } else {
        $csv = shell_exec("tail -n+2 $file");
    }
        
    $array = str_getcsv($csv, "\n");
        
    $mapArray = [];
        
    foreach($array as $Row) {
        $mapArray[] = array_combine($header, str_getcsv($Row, ','));
    }
    
    return $mapArray;
}
