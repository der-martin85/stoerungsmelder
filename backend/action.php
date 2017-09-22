<?php

require_once 'getdata.php';
require_once 'mysqlconfig.php';


use Aura\Sql\ExtendedPdo;
use Aura\SqlQuery\QueryFactory;


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

/**
 * @param array $auftrag    associative array containing: name, email, von, bis, wochtentage (array), tolerance 
 */

function speicherSuchauftrag(array $auftrag, QueryFactory $queryFactory, ExtendedPdo $pdo): array 
{
    try {
        $selectUser = $queryFactory->newSelect();
        $selectUser
        ->cols(['id'])
        ->from('user')
        ->where('email = :email')
        ->bindValue('email', $auftrag['email']);
        if ($result = $pdo->fetchOne($selectUser->getStatement(), $selectUser->getBindValues())) {
            $userid = $result['id'];
        } else {
                
            $insertUser = $queryFactory->newInsert();
            $insertUser
            ->into('user')                   // INTO this table
            ->cols([                        // bind values as "(col) VALUES (:col)"
                'name',
                'email',
            ])
            ->bindValue('name', $auftrag['name'])
            ->bindValue('email', $auftrag['email']);
            $pdo->perform($insertUser->getStatement(), $insertUser->getBindValues());
            $name = $insertUser->getLastInsertIdName('id');
            $userid = $pdo->lastInsertId($name);
        }
        
        $insertAuftrag = $queryFactory->newInsert();
        $insertAuftrag
        ->into('auftrag');                   // INTO this table
        foreach ($auftrag['wochentage'] as $wochentag) {
            $insertAuftrag->addRow([
                'user' => $userid,
                'von' => $auftrag['von'],
                'bis' => $auftrag['bis'],
                'wochentag' => $wochentag,
                'tolerance' => $auftrag['tolerance']
            ]);
        }
        $pdo->perform($insertAuftrag->getStatement(), $insertAuftrag->getBindValues());
    } catch (PDOException $e) {
        return [
            "SUCCESS" => false,
            "ERR_MESSAGE" => $e->getCode().": ".$e->getMessage()."\n".$e->getTraceAsString()
        ];
    }
    
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
            //$auftrag = json_decode($_REQUEST['auftrag'], true);
            $auftrag = [
                "name" => "Martin",
                "email" => "martin@martimedia.de",
                "von" => "06:00:00",
                "bis" => "09:00:00",
                "wochentage" => [
                    1, 2, 3, 4, 5
                ],
                "tolerance" => 15
            ];
            $return = speicherSuchauftrag($auftrag, $queryFactory, $pdo);
            break;
        default:
            $return = [];
    }
}

echo json_encode($return);
echo "</pre>";