<?php

require __DIR__ . '/vendor/autoload.php';

// $spreadsheetId = '12f2WBV2VJStDefgCQBrPG2xWNVCC9n2flwIK-JvUjZI'; // test sheet
$spreadsheetId = '13cBp0oCkmeABBWDbySnwTF2Nr3Eqz38baqrTOK-Yqu0';
$ios_sheetName = "iOS";
$web_sheetName = "EN data";
$ios_sheetId = 0;
$web_sheetId = 263246219;
$range = "A1"; // update full sheet
$ios_update_range = $ios_sheetName . "!" . $range;
$web_update_range = $web_sheetName . "!" . $range;

$client = new \Google_Client();
$client->setApplicationName('WA Notify Updater');
$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
$client->setAccessType('offline');
$client->setAuthConfig(__DIR__ . '/enxsheets-credentials.json');

$service;
try {
    $service = new Google_Service_Sheets($client);
} catch (Exception $e) {
    echo "Error creating service:\n";
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

$time_finish = new DateTime(); // now
$time_interval = new DateInterval('PT1H');

$installs_time_start = date_create_from_format("Y-m-d H:i:s", "2020-11-30 00:00:00");
$installs_datetimes = generateDateTimes($installs_time_start, $time_finish, $time_interval);

$web_time_start = date_create_from_format("Y-m-d H:i:s", "2020-11-01 00:00:00");
$web_datetimes = generateDateTimes($web_time_start, $time_finish, $time_interval);

$strAnalyticsData = file_get_contents('./resource_stats_hourly.json');
$jsondata = json_decode($strAnalyticsData, true);
// prep data for entry
$data_by_datetime = array();
$install_data_fields = array();
$web_data_fields = array();
foreach ($jsondata as $d) {
    $resource = str_replace("iOS installs ", "", $d['resource']);
    $data_by_datetime[date_create_from_format('Y-m-d H', $d['time'])->format('Y-m-d H:i:s')][$resource] = $d;
    if (substr($d['resource'],0,1) === "/") { // Web page/resource hit
        $web_data_fields[$resource] = 0;
    } else if ($resource != "HealthENBuddy") { // HealthENBuddy added to data_fields later
        $install_data_fields[$resource] = 0;
    }
}
$install_data_fields = array_keys($install_data_fields);
array_multisort($install_data_fields);
array_unshift($install_data_fields, "HealthENBuddy");
array_unshift($install_data_fields, "Settings Total");
$web_data_fields = array_keys($web_data_fields);
array_multisort($web_data_fields);

$new_ios_values = array();
foreach ($installs_datetimes as $datetime) {
    $ios_row = array();
    if (isset($data_by_datetime[$datetime])) {
        $settings_total = 0;
        foreach ($install_data_fields as $field) {
            $value = $data_by_datetime[$datetime][$field]['200'] ?? 0;
            if (!($field == "HealthENBuddy" || preg_match("/[^a-zA-Z\d%]/", $field))) {
                $settings_total += $value;
            }
            $ios_row[] = $value;
        }
        $ios_row[0] = $settings_total; // Fill in Settings sum column
    }
    array_unshift($ios_row, $datetime); // Add datetime to first col
    $new_ios_values[] = $ios_row;
}

array_unshift($install_data_fields, "Date Time");

// Get ios useragent language mapping
$language_range = "UserAgentTranslations!A2:B50";
$language_values;
try {
    $response = $service->spreadsheets_values->get($spreadsheetId, $language_range);
    $language_values = $response->getValues();
} catch (Exception $e) {
    echo "Error getting languages:\n";
    echo $e."\n";
    echo "Retrying...\n";
    sleep(30);
    try {
        $response = $service->spreadsheets_values->get($spreadsheetId, $language_range);
        $language_values = $response->getValues();
    } catch (Exception $e) {
        "Failed.\n";
        exit;
    }
}
$useragent_mappings = array();
foreach ($language_values as $v) {
    $useragent_mappings[$v[1]] = $v[0];
}
foreach ($install_data_fields as $i => $field) {
    $install_data_fields[$i] = $useragent_mappings[$field] ?? $install_data_fields[$i];
}

array_unshift($new_ios_values, $install_data_fields);

$new_web_values = array();
foreach ($installs_datetimes as $datetime) {
    $web_row = array();
    if (isset($data_by_datetime[$datetime])) {
        foreach ($web_data_fields as $field) {
            $value = $data_by_datetime[$datetime][$field]['200'] ?? 0;
            $web_row[] = $value;
        }
    }
    array_unshift($web_row, $datetime); // Add datetime to first col
    $new_web_values[] = $web_row;
}

array_unshift($web_data_fields, "Date Time");

array_unshift($new_web_values, $web_data_fields);

// Clear the ios sheet of all old values
$requests = [
    new Google_Service_Sheets_Request([
        'updateCells' => [
            'range' => [
                'sheetId' => $ios_sheetId
            ],
            'fields' => '*'
        ]
    ])
];
$batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
    'requests' => $requests
]);

try {
    $response = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
} catch (Exception $e) {
    echo "Error deleting ios data:\n";
    echo $e."\n";
    echo "Retrying...\n";
    sleep(30);
    try {
        $response = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
    } catch (Exception $e) {
        "Failed.\n";
        exit;
    }
}

// Add the new ios values to the sheet
$body = new Google_Service_Sheets_ValueRange([
    'values' => $new_ios_values
]);
$params = [
    'valueInputOption' => 'USER_ENTERED'
];

try {
    $response = $service->spreadsheets_values->update($spreadsheetId, $ios_update_range, $body, $params);
} catch (Exception $e) {
    echo "Error writing ios data:\n";
    echo $e."\n";
    echo "Retrying...\n";
    sleep(30);
    try {
        $response = $service->spreadsheets_values->update($spreadsheetId, $ios_update_range, $body, $params);
    } catch (Exception $e) {
        "Failed.\n";
        exit;
    }
}

// Clear the web sheet of all values
$requests = [
    new Google_Service_Sheets_Request([
        'updateCells' => [
            'range' => [
                'sheetId' => $web_sheetId
            ],
            'fields' => '*'
        ]
    ])
];
$batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
    'requests' => $requests
]);
try {
    $response = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
} catch (Exception $e) {
    echo "Error deleting web data:\n";
    echo $e."\n";
    echo "Retrying...\n";
    sleep(30);
    try {
        $response = $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
    } catch (Exception $e) {
        "Failed.\n";
        exit;
    }
}

// Add the new web values to the sheet
$body = new Google_Service_Sheets_ValueRange([
    'values' => $new_web_values
]);
$params = [
    'valueInputOption' => 'USER_ENTERED'
];
try {
    $result = $service->spreadsheets_values->update($spreadsheetId, $web_update_range, $body, $params);
} catch (Exception $e) {
    echo "Error writing web data:\n";
    echo $e."\n";
    echo "Retrying...\n";
    sleep(30);
    try {
        $result = $service->spreadsheets_values->update($spreadsheetId, $web_update_range, $body, $params);
    } catch (Exception $e) {
        "Failed.\n";
        exit;
    }
}

// Update sheet iOS timestamp
$now_dt = new DateTime();
$timestamp_update_range = "Summary!B1";
$body = new Google_Service_Sheets_ValueRange([
    'values' => array(array($now_dt->format('Y-m-d H:i:s')))
]);
$params = [
	'valueInputOption' => 'USER_ENTERED'
];
try {
	$result = $service->spreadsheets_values->update($spreadsheetId, $timestamp_update_range, $body, $params);
} catch (Exception $e) {
	echo "Error writing timestamp:\n";
	echo $e."\n";
	echo "Retrying...\n";
	sleep(30);
	try {
		$result = $service->spreadsheets_values->update($spreadsheetId, $timestamp_update_range, $body, $params);
	} catch (Exception $e) {
		"Failed.\n";
		exit;
	}
}

function generateDateTimes($time_start, $time_finish, $time_interval) {
    $datetimes = array();
    $datetime = $time_start;
    while ($datetime < $time_finish) {
        $datetimes[] = $datetime->format("Y-m-d H:i:s");
        $datetime->add($time_interval);
    }
    return $datetimes;
}
?>
