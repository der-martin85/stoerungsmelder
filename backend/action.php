<?php

include 'getdata.php';

function sucheLinien(string $linie):array {
    $lines = getData(ROUTES_FILE, $linie);
    
    $ret = [];
    
    foreach ($lines as $line) {
        if (stripos($line['route_short_name'], $linie) !== false) {
            $info = [
                "linienId" => $line['route_id'],
                "linienName" => $line['route_short_name']." (".$line['route_long_name'].")"
            ];
            array_push($ret, $info);
        }
    }
    unset($lines);
    
    return $ret;
}

function sucheHaltestellen(string $linienId):array {
    $trips = getData(TRIPS_FILE, $linienId);
    $tripIds = [];
    
    foreach ($trips as $trip) {
        if ($trip['route_id'] == $linienId) {
            $tripIds[] = $trip['trip_id'];
        }
    }
    unset($trips);
    //echo var_dump($tripIds);
    $stopIds = [];
    
    foreach ($tripIds as $tripId) {
        $stop_times = getData(STOP_TIME_FILE, $tripId);
        
        foreach ($stop_times as $stop_time) {
            if ($stop_time['trip_id'] == $tripId) {
                if (!in_array($stop_time['stop_id'], $stopIds)) {
                    $stopIds[] = $stop_time['stop_id'];
                }
            }
        }
        unset($stop_times);
    }
    
    $ret = [];
    
    foreach ($stopIds as $stopId) {
        $stops = getData(STOPS_FILE, $stopId);
        foreach ($stops as $stop) {
            if ($stop['stop_id'] == $stopId) {
                $info = [
                    "HaltestId" => $stop['stop_id'],
                    "Name" => $stop['stop_name']
                ];
                if (!in_array($info, $ret)) {
                    $ret[] = $info;
                }
            }
        }
        unset($stops);
    }
    
    return $ret;
}

function speicherSuchauftrag(
    array $wochentage, 
    int $zeitVonStunde, 
    int $zeitVonMinuten, 
    int $zeitBisStunden, 
    int $zeitBisMinuten, 
    int $vonHaltestellenId, 
    int $bisHaltestellenId, 
    string $user): array 
{
    
    return ["SUCCESS" => true];
}

$return = [];

echo "<pre>";

if (isset($_REQUEST["action"])) {
    switch ($_REQUEST["action"]) {
        case "sucheLinien":
            if (isset($_REQUEST['linie'])) {
                $return = sucheLinien($_REQUEST['linie']);
            }
            break;
        case "sucheHaltestellen":
            if (isset($_REQUEST['linienId'])) {
                $return = sucheHaltestellen($_REQUEST['linienId']);
            }
            break;
        case "speicheSuchauftrag":
            $wochentage = $_REQUEST['wochentage'];
            $zeitVonStunde = $_REQUEST['vonStunde'];
            $zeitVonMinuten = $_REQUEST['vonMinute'];
            $zeitBisStunden = $_REQUEST['bisStunde'];
            $zeitBisMinuten = $_REQUEST['bisMinute'];
            $vonHaltestellenId = $_REQUEST['vonHaltestelle'];
            $bisHaltestellenId = $_REQUEST['bisHaltestelle'];
            $user = $_REQUEST['user'];
            $return = speicherSuchauftrag($wochentage, $zeitVonStunde, $zeitVonMinuten, $zeitBisStunden, $zeitBisMinuten, $vonHaltestellenId, $bisHaltestellenId, $user);
            break;
        default:
            $return = [];
    }
}

echo json_encode($return);
echo "</pre>";