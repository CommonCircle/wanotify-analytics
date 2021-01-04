<?php

require __DIR__ . '/vendor/autoload.php';

$tz = 'US/Pacific';
$dt = new DateTime('now', new DateTimeZone($tz));
$time = $dt->format('m/d/Y');

$spreadsheet_id = '13cBp0oCkmeABBWDbySnwTF2Nr3Eqz38baqrTOK-Yqu0';
$sheet_name = "Summary";
$range = "B6:B8"; // summary totals
$get_range = $sheet_name . "!" . $range;

$client = new \Google_Client();
$client->setApplicationName('WA Notify Summary');
$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
$client->setAccessType('offline');
$client->setAuthConfig(__DIR__ . '/enxsheets-credentials.json');

$service;
try {
    $service = new Google_Service_Sheets($client);
} catch (Exception $e) {
    echo "Error connecting to service:\n";
    echo $e."\n";
    echo "Retrying...\n";
    sleep(30);
    try {
        $service = new Google_Service_Sheets($client);
    } catch (Exception $e) {
        "Failed.\n";
        exit;
    }
}

$values;
try {
    $response = $service->spreadsheets_values->get($spreadsheet_id, $get_range);
    $values = $response->getValues();
} catch (Exception $e) {
    echo "Error connecting to service:\n";
    echo $e."\n";
    echo "Retrying...\n";
    sleep(30);
    try {
        $response = $service->spreadsheets_values->get($spreadsheet_id, $get_range);
        $values = $response->getValues();
    } catch (Exception $e) {
        "Failed.\n";
        exit;
    }
}
//var_dump($values);
$ios = $values[0][0];
$android = $values[1][0];
$total = $values[2][0];

$precision = 10000;
$lt_threshold = 4500;
$est = $total - ($total % $precision);
$diff = $total - $est;
$sign;
if ($diff < ($precision - $lt_threshold)) {
    $sign = "More than ";
} else {
    $sign = "Nearly ";
    $est = $est+$precision;
}

$est = $est/(10**6);
$est = $sign . $est;

$to = "bryant.karras@doh.wa.gov, amy.reynolds@doh.wa.gov, dlorigan@uw.edu, lober@uw.edu, jbaseman@uw.edu";
//$to = "dlorigan@uw.edu";
$subject = "WA Notify Report - $time";
$txt = '';
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type:text/html;charset=UTF-8\r\n";
$headers .= "From: <wanotify@cirg.washington.edu>\r\n";
$message = "
<html>
<head>
<title>WA Notify Summary, $time</title>
</head>
<body>
<p>WA Notify user counts as of {$dt->format('m/d/Y H:i')}</p>
<table>
<tr><th style='text-align:left'>Estimate</th><td style='text-align:right'>$est million</td></tr>
<tr><th style='text-align:left'>Total Users</th><td style='text-align:right'>".number_format($total)."</td></tr>
<tr><th style='text-align:left'>iOS Users</th><td style='text-align:right'>".number_format($ios)."</td></tr>
<tr><th style='text-align:left'>Android Users</th><td style='text-align:right'>".number_format($android)."</td></tr>
</table>
<br>
<p>Detailed Statistics:<br>https://docs.google.com/spreadsheets/d/$spreadsheet_id</p>
</body>
</html>
";

mail($to, $subject, $message, $headers);
?>
