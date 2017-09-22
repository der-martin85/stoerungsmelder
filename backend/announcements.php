<?php
//extract data from the post
//set POST variables
$url = 'http://api-hack.geofox.de/gti/public/getAnnouncements';
$fields = array(
);
$user = 'mobi-hack';
$password = 'H4m$urgH13okt';

$begin = date_create('2017-08-01');
$end = date_create('2017-10-01');

$payload = [
//    "names" => ["S3", "S1", "S31", "21"],
    "timeRange" => [
        "begin" => date_format($begin, 'Y-m-d\TH:i:s.000O'),
        "end" => date_format($end, 'Y-m-d\TH:i:s.000O')
    ],
    "full" => true
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

//echo var_dump($res);

$announcements = $res['announcements'];

foreach ($announcements as $entry) {
    echo $entry['summary']."\n";
    echo $entry['description']."\n";
    echo "Betrifft Linien:\n";
    foreach ($entry['locations'] as $location) {
        echo $location['name']."\n";
    }
}
echo "</pre>";

