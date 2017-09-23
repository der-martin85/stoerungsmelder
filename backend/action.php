<?php

require_once 'getdata.php';
require_once 'mysqlconfig.php';


use Aura\Sql\ExtendedPdo;
use Aura\SqlQuery\QueryFactory;


function sucheLinien(string $linie, QueryFactory $queryFactory, ExtendedPdo $pdo):array {
    $lines = getData(ROUTES_FILE, $linie);
    
    $selectRoutes = $queryFactory->newSelect();
    $selectRoutes
    ->cols([
        'route_id' => 'id',         
        'CONCAT(route_short_name, " (", route_long_name, ")")' => 'name'
    ])
    ->from('routes')
    ->where('route_short_name LIKE :linie')
    ->bindValue('linie', "%$linie%");
    $result = $pdo->fetchAll($selectRoutes->getStatement(), $selectRoutes->getBindValues());
    
    return $result;
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
    
    $stops = getData(STOPS_FILE);
    foreach ($stopIds as $stopId) {
        foreach ($stops as $stop) {
            if ($stop['stop_id'] == $stopId) {
                $info = [
                    "id" => $stop['stop_id'],
                    "name" => $stop['stop_name']
                ];
                if (!in_array($info, $ret)) {
                    $ret[] = $info;
                }
            }
        }
    }
    unset($stops);
    
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
                'tolerance' => $auftrag['tolerance'],
                'warnart' => $auftrag['warnart'],
                'infozeit' => $auftrag['infozeit'],
                'startHlt' => $auftrag['startHlt'],
                'EndHlt' => $auftrag['EndHlt'],
                'linie' => $auftrag['linie'],
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



if (isset($_REQUEST["action"])) {
    switch ($_REQUEST["action"]) {
        case "sucheLinien":
            if (isset($_REQUEST['linie'])) {
                $return = sucheLinien($_REQUEST['linie'], $queryFactory, $pdo);
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
                "tolerance" => 15,
                "warnart" => 0,
                "infozeit" => "05:30",
                "startHlt" => "423423",
                "EndHlt" => "342",
                "linie" => "324234"
                ];
            $return = speicherSuchauftrag($auftrag, $queryFactory, $pdo);
            break;
        default:
            $return = [];
    }
}
function encode_items(&$item, $key)
{
    $item = utf8_encode($item);
}
array_walk_recursive($return, 'encode_items');

header("Content-type:application/json; charset=utf-8");
echo json_encode($return, JSON_FORCE_OBJECT);
