<?php

require __DIR__ . '/vendor/autoload.php';

$time_start = date_create_from_format("Y-m-d H:i:s", "2020-11-30 00:00:00");
$time_finish = new DateTime(); // now

$datetimes = array();
$datetime = $time_start;
$dateinterval = new DateInterval('PT1H');
while ($datetime < $time_finish) {
    $datetimes[] = $datetime->format("Y-m-d H:i:s");
    $datetime->add($dateinterval);
}

$client = new \Google_Client();
$client->setApplicationName('Google Reports API PHP');
$client->setScopes([\Google_Service_Reports::ADMIN_REPORTS_AUDIT_READONLY]);
$client->setAccessType('offline');
$client->setAuthConfig(__DIR__ . '/wanotifyreports-credentials.json');

$service = new Google_Service_Reports($client);


?>