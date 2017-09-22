<?php 

require_once 'vendor/autoload.php';

use Aura\Sql\ExtendedPdo;
use Aura\SqlQuery\QueryFactory;

const DB_USERNAME = "stoerungsmelder";
const DB_PASSWORD = "UvlDecxvQ35MCKg1";
const DB_NAME = "stoerungsmelder";

$pdo = new ExtendedPdo(
    'mysql:host=localhost;dbname='.DB_NAME,
    DB_USERNAME,
    DB_PASSWORD
    );

$queryFactory = new QueryFactory('mysql');
