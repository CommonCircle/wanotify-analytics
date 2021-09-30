<?php

$config = include __DIR__ . '/Configuration/exporter_config.php';
$pdo = include __DIR__ . '/Configuration/database_config.php';

require_once VENDOR_DIR . '/autoload.php';

// Set up sheets client
$client = new \Google_Client();
$client->setApplicationName('ENX Sheets Updater');
$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
$client->setAccessType('offline');
$client->setAuthConfig($config->credentials_file);

try {
    $service = new Google_Service_Sheets($client);
} catch (Exception $e) {
    echo "Error creating service:\n";
    echo $e."\n";
    echo "Retrying...\n";
    sleep(20);
    $service = new Google_Service_Sheets($client);
}

$spreadsheetId = $config->spreadsheet_id;

foreach ($config->sheets as $name => $values) {
    // Read configuration for this destination
    $dataTypeSuffix = $values['data_type'];
    $startTime = $values['start_time'];
    $dataInterval = $values['data_interval'];
    $sheetName = $values['sheet_name'];
    $headerRange = $sheetName . "!A1:1";
    $dataRange = $sheetName;

    // Load exporter class file
    $exporterClassName = "Exporter_GoogleSheets_$dataTypeSuffix";
    include(EXPORTER_DIR . "/$exporterClassName.php");
    $exporter = new $exporterClassName($pdo);

    // Get contents from data store
    $contents = $exporter->export($dataInterval, $startTime);
    $fields = array_shift($contents);

    // Get sheet's current header row
    try {
        $response = $service->spreadsheets_values->get($spreadsheetId, $headerRange);
        $sheetHeader = $response->getValues()[0];
    } catch (Exception $e) {
        echo "Error getting current $dataTypeSuffix values:\n";
        echo $e."\n";
        echo "Retrying...\n";
        sleep(20);
        try {
            $response = $service->spreadsheets_values->get($spreadsheetId, $headerRange);
            $sheetHeader = $response->getValues()[0];
        } catch (Exception $e) {
            echo("Failed.\n$e\n");
            continue;
        }
    }
    
    if ($sheetHeader) {
        // Add new fields to end of existing series to maintain order in sheet
        $fields = array_unique(array_merge($sheetHeader, $fields));
    }

    // Reorder data store row contents to match header
    $fieldOrderTemplate = array_fill_keys($fields, "0");
    $newRows = array(array_values($fields));
    foreach ($contents as $row) {
        // var_dump(json_encode($fieldOrderTemplate), json_encode($row), json_encode(array_replace($fieldOrderTemplate, $row)));
        $newRows[] = array_values(array_replace($fieldOrderTemplate, $row));
        // var_dump(json_encode($newRows));
        // exit;
    }

    // var_dump(json_encode(array_slice($newRows,0,3)));
    // exit;

    // Update the sheet
    $body = new Google_Service_Sheets_ValueRange([
        'values' => $newRows
    ]);
    $params = [
        'valueInputOption' => 'USER_ENTERED'
    ];

    try {
        $response = $service->spreadsheets_values->update($spreadsheetId, $dataRange, $body, $params);
    } catch (Exception $e) {
        echo "Error writing data\n SPREADSHEET_ID: $spreadsheetId\n RANGE: $dataRange\n";
        echo $e."\n";
        echo "Retrying...\n";
        sleep(30);
        try {
            $response = $service->spreadsheets_values->update($spreadsheetId, $dataRange, $body, $params);
        } catch (Exception $e) {
            echo "Failed.\n$e\n";
            continue;
        }
    }
}