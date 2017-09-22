<?php
require_once 'vendor/autoload.php';
require_once 'mailconfig.php';

use PHPMailer\PHPMailer\PHPMailer;

//extract data from the post
//set POST variables
$url = 'http://api-hack.geofox.de/gti/public/getAnnouncements';
$fields = array(
);
$user = 'mobi-hack';
$password = 'H4m$urgH13okt';

$begin = date_create('2017-08-01');
$end = date_create('2017-10-01');
$lines = ["36"];

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

$body = json_encode($payload);

echo "<pre>";

//echo $body;

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

echo var_dump($res);

$announcements = $res['announcements'];

$emailbody = "Folgende Störungen wurden auf der Route gefunden:\n";

foreach ($announcements as $entry) {
    $emailbody .= $entry['summary']."\n";
    $emailbody .= $entry['description']."\n";
    $emailbody .= "Grund: ".$entry['reason']."\n";
    $emailbody .= "Betrifft Linien:\n";
    foreach ($entry['locations'] as $location) {
        $emailbody .= $location['name']."\n";
    }
}

echo $emailbody;

$subject = "Störungsmeldungen";
$userEmail = "martin@martimedia.de";
$userName = "Martin Ringwelski";

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

echo "</pre>";



