<?php

$config = include(__DIR__ . '/config.php');
$pdo = include(__DIR__ . '/database.php');

foreach ($config->sources as $type => $values) {
    // Read data source configuration
    $location = $values['location'] ?? null;
    $auth_type = $values['auth_type'] ?? null;
    $auth_value = $values['auth_value'] ?? null;

    // Load importer class file
    $importerClassName = "Importer_$type";
    include(IMPORTER_DIR . "/$importerClassName.php");
    $importer = new $importerClassName($pdo, $location, $auth_type, $auth_value);

    // Load the data
    $importer->import();
}
