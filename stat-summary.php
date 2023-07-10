<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/recipientList.php';

// Debug override
// $recipientList = $errorRecipientList;

$tz = 'US/Pacific';
$dt = new DateTime('now', new DateTimeZone($tz));
$time = $dt->format('m/d/Y');

$spreadsheet_id = '1WyK1B1ik9t5y2dMcNbO8pj8D8Na7khpeazertAGq1c0';
$sheet_name = "'Scalars'";
$range = "A1:B18"; // summary totals
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
    // echo "Error connecting to service:\n";
    // echo $e."\n";
    // echo "Retrying...\n";
    // sleep(30);
    try {
        $service = new Google_Service_Sheets($client);
    } catch (Exception $e) {
        echo "Failed to data.\n$e\n";
        exit;
    }
}

$sheet_values;
try {
    $response = $service->spreadsheets_values->get($spreadsheet_id, $get_range);
    $sheet_values = $response->getValues();
} catch (Exception $e) {
    // echo "Error connecting to service:\n";
    // echo $e."\n";
    // echo "Retrying...\n";
    // sleep(30);
    try {
        $response = $service->spreadsheets_values->get($spreadsheet_id, $get_range);
        $sheet_values = $response->getValues();
    } catch (Exception $e) {
        echo "Failed to get current data.\n$e\n";
        exit;
    }
}

$values = array();
foreach ($sheet_values as $row) {
    if (sizeof($row) == 2) {
        // is data row
        if ($row[1] && $row[1] != "#VALUE!") {
            // all data rows should have a value
            $values[$row[0]] = $row[1];
        } else {
            // bad sheet data fetch, cancel, notify and run again manually
            $subject = "WA Notify Report - Error";
            $message = "There was a problem fetching summary values:\n\n" . json_encode($sheet_values);
            $message .= "<p>Detailed Statistics:<br>https://docs.google.com/spreadsheets/d/$spreadsheet_id</p>";
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8\r\n";
            $headers .= "From: <wanotify@cirg.washington.edu>\r\n";
            mail($errorRecipientList, $subject, $message, $headers);
            exit;
        }
    }
}

if (isset($values["Total iOS Activations"], $values["Total Android Activations"], $values["Total Activations"], $values["Rate in smartphone owners"])) {
	$ios = $values["Total iOS Activations"];
	$android = $values["Total Android Activations"];
	$total = $values["Total Activations"];
	$smartphone_rate = $values["Rate in smartphone owners"];
} else {
	$subject = "WA Notify Report - Error";
	$message = "There was a problem fetching summary values:\n\n" . json_encode($sheet_values);
	$message .= "<p>Detailed Statistics:<br>https://docs.google.com/spreadsheets/d/$spreadsheet_id</p>";
	$headers = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8\r\n";
	$headers .= "From: <wanotify@cirg.washington.edu>\r\n";
	mail($errorRecipientList, $subject, $message, $headers);
	exit;
}

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
<p>WA Notify Activation Counts:</p>
<table>
<tr><th style='text-align:left'>Date/Time</th><td style='text-align:right'>{$dt->format('m/d/Y H:i')}</td></tr>
<tr><th style='text-align:left'>Estimate</th><td style='text-align:right'>$est million</td></tr>
<tr><th style='text-align:left'>App Activations</th><td style='text-align:right'>".number_format($total)."</td></tr>
<tr><th style='text-align:left'>iOS Activations</th><td style='text-align:right'>".number_format($ios)."</td></tr>
<tr><th style='text-align:left'>Android Activations</th><td style='text-align:right'>".number_format($android)."</td></tr>
<tr><th style='text-align:left'>Activation Rate in smartphone owners</th><td style='text-align:right'>".$smartphone_rate."</td></tr>
</table>
<br>
<p>Detailed Statistics:<br>https://docs.google.com/spreadsheets/d/$spreadsheet_id</p>
</body>
</html>
";

mail($recipientList, $subject, $message, $headers);
?>
