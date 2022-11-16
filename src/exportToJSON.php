<?php

$config = include __DIR__ . '/Configuration/exporter_config.php';
$pdo = include __DIR__ . '/Configuration/database_config.php';

$all_data = array();
foreach ($config->files as $name => $values) { // TODO support multiple files
    // Read configuration for this destination
    $dataTypeSuffix = $values['data_type'];
    $dataInterval = $values['data_interval'];
    $startTime = $values['start_time'];
    $endTime = $values['end_time'] ?? null;

    // Load exporter class file
    $exporterClassName = "Exporter_JSON_$dataTypeSuffix";
    include(EXPORTER_DIR . "/$exporterClassName.php");
    $exporter = new $exporterClassName($pdo);

    // Get contents from data store
    $contents = $exporter->export($dataInterval, $startTime, $endTime);
    if (array_key_exists($dataTypeSuffix, $contents)) {
        foreach ($contents as $type => $data) {
            $all_data[$type] = $data;
        }
        $contents = $contents[$dataTypeSuffix];
    } else {
        $all_data[$dataTypeSuffix] = $contents;
    }

    // Write to the file
    $file_out = OUTPUT_DIR . "/$name.json";
    $file_handle = fopen($file_out, 'w');
    // echo "Resource data: $file_out\n";
    fwrite($file_handle, json_encode(array($dataTypeSuffix => $contents), JSON_PRETTY_PRINT));
}

// Write combined data to the file
$file_out = OUTPUT_DIR . "/All_daily.json";
$file_handle = fopen($file_out, 'w');
// echo "All resource data: $file_out\n";
fwrite($file_handle, json_encode($all_data, JSON_PRETTY_PRINT));
