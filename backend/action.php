<?php

function sucheLinien(string $linie):array {
    return [
        "linienId" => 1,
        "linienName" => "S3"
    ];
}

function sucheHaltestellen(int $linienId):array {
    return [
        [
            "HaltestId" => 1,
            "Name" => "Neuwiedental"
        ],
        [
            "HaltestId" => 2,
            "Name" => "Hausbruch"
        ],
        [
            "HaltestId" => 3,
            "Name" => "Heimfeld"
        ],
        [
            "HaltestId" => 4,
            "Name" => "Harburg Rathaus"
        ]
    ];
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
