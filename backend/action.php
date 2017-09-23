<?php

require_once 'mysqlconfig.php';


use Aura\Sql\ExtendedPdo;
use Aura\SqlQuery\QueryFactory;


function sucheLinien(string $linie, QueryFactory $queryFactory, ExtendedPdo $pdo):array {
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

function sucheHaltestellen(string $linienId, QueryFactory $queryFactory, ExtendedPdo $pdo):array {
    $selectStops = $queryFactory->newSelect();
    $selectStops
    ->cols([
        'stops.stop_id' => 'id',
        'stops.stop_name' => 'name'
    ])
    ->from('trips')
    ->join(
        'INNER', 
        'stop_times',
        'stop_times.trip_id = trips.trip_id'
        )
    ->join(
        'INNER',
        'stops',
        'stops.stop_id = stop_times.stop_id'
        )
    ->where('route_id = :linienId')
    ->bindValue('linienId', $linienId)
    ->orderBy(['stops.stop_name'])
    ->groupBy(['stops.stop_id']);
    $result = $pdo->fetchAll($selectStops->getStatement(), $selectStops->getBindValues());
    
    return $result;
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
                $return = sucheHaltestellen($_REQUEST['linienId'], $queryFactory, $pdo);
            }
            break;
        case "speicheSuchauftrag":
            $auftrag = json_decode($_REQUEST['auftrag'], true);
//             $auftrag = [
//                 "name" => "Martin",
//                 "email" => "martin@martimedia.de",
//                 "von" => "06:00:00",
//                 "bis" => "09:00:00",
//                 "wochentage" => [
//                     1, 2, 3, 4, 5
//                 ],
//                 "tolerance" => 15,
//                 "warnart" => 0,
//                 "infozeit" => "05:30",
//                 "startHlt" => "423423",
//                 "EndHlt" => "342",
//                 "linie" => "324234"
//                 ];
            $return = speicherSuchauftrag($auftrag, $queryFactory, $pdo);
            break;
        default:
            $return = [];
    }
}

header("Content-type:application/json; charset=utf-8");
echo json_encode($return);
