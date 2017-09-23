<pre>
<?php
require_once 'mysqlconfig.php';
require_once 'mailconfig.php';

use PHPMailer\PHPMailer\PHPMailer;

$userId = 1;
if (isset($_REQUEST['userid'])) {
    $userId = $_REQUEST['userid'];
}

$userEmail = "martin@martimedia.de";
$userName = "Martin Ringwelski";
$lines = [];
$weekday = date('w');

$selectUser = $queryFactory->newSelect();
$selectUser
->cols(['email', 'name'])
->from('user')
->where('id = :id')
->bindValue('id', $userId);
if ($result = $pdo->fetchOne($selectUser->getStatement(), $selectUser->getBindValues())) {
    $userEmail = $result['email'];
    $userName = $result['name'];
}

$selectAuftraege = $queryFactory->newSelect();
$selectAuftraege
->cols([
    'von',
    'bis',
    'linie',
    'startHlt',
    'EndHlt',
    'warnart',
    'tolerance',
    'infozeit'
])
->from('auftrag')
->where('user = :id')
->where('wochentag = :wochentag')
->bindValue('id', $userId)
->bindValue('wochentag', $weekday);
$result = $pdo->fetchAll($selectAuftraege->getStatement(), $selectAuftraege->getBindValues());

$selectLinie = $queryFactory->newSelect();
$selectLinie
->cols(['route_short_name'])
->from('routes')
->where('route_id = :id');
foreach($result as $row) {
    $selectLinie->bindValue('id', $row['linie']);
    $result = $pdo->fetchOne($selectLinie->getStatement(), $selectLinie->getBindValues());
    $linienName = $result['route_short_name'];
    if (!in_array($linienName, $lines)) {
        $lines[] = $result['route_short_name'];
    }
    $newBegin = date_create(date('Y-m-d').' '.$row['von']);
    if (!isset($begin) || $begin > $newBegin) {
        $begin = $newBegin;
    }
    $newEnd = date_create(date('Y-m-d').' '.$row['bis']);
    if (!isset($end) || $end < $newEnd) {
        $end = $newEnd;
    }
}

//extract data from the post
//set POST variables
$url = 'http://api-hack.geofox.de/gti/public/getAnnouncements';
$fields = array(
);
$user = 'mobi-hack';
$password = 'H4m$urgH13okt';

$payload = [
    "names" => $lines,
    "timeRange" => [
        "begin" => date_format($begin, 'Y-m-d\TH:i:s.000O'),
        "end" => date_format($end, 'Y-m-d\TH:i:s.000O')
    ],
    "full" => true,
    "version" => 30
];

//open connection
$ch = curl_init();

$body = json_encode($payload)."\n";


echo $body;

$signature = base64_encode(hash_hmac('sha1', $body, $password, true));

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POSTFIELDS, $body );
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true );
curl_setopt($ch,CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json;charset=UTF-8',
    'geofox-auth-type: HmacSHA1',
    'geofox-auth-user: '.$user,
    'geofox-auth-signature: '.$signature
));



//execute post
$result = curl_exec($ch);

//close connection
curl_close($ch);

$res = json_decode($result, true);

//echo var_dump($res);

if (isset($res['announcements'])) {
    
    $announcements = $res['announcements'];
    
    $emailbody = "Folgende Störungen wurden auf der Route gefunden:\n";
    
    foreach ($announcements as $entry) {
        $emailbody .= $entry['summary']."\n";
        $emailbody .= $entry['description']."\n";
        $emailbody .= "Grund: ".$entry['reason']."\n";
        $emailbody .= "Betrifft Linien:\n";
        foreach ($entry['locations'] as $location) {
            $emailbody .= $location['line']['name']."\n";
        }
    }
    
    echo $emailbody;
    
    $subject = "Störungsmeldungen";
    
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;                // enable SMTP authentication
    $mail->SMTPSecure = "tls";              // sets the prefix to the servier
    $mail->Host = MAIL_HOST;       
    $mail->Port = 587;             
    
    $mail->Username = MAIL_USERNAME;
    $mail->Password = MAIL_PASSWORD;
    
    $mail->CharSet = 'utf-8';
    $mail->SetFrom (MAIL_USERNAME, 'Störungsmelder');
    $mail->Subject = $subject;
    $mail->ContentType = 'text/plain';
    $mail->IsHTML(false);
    
    $mail->Body = $emailbody;
    // you may also use $mail->Body = file_get_contents('your_mail_template.html');
    
    $mail->AddAddress ($userEmail, $userName);
    // you may also use this format $mail->AddAddress ($recipient);
    
    if(!$mail->Send())
    {
        $error_message = "Mailer Error: " . $mail->ErrorInfo;
    } else
    {
        $error_message = "Successfully sent!";
    }
    echo $error_message;
}

echo "</pre>";



