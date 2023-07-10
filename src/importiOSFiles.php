<?php

$config = include __DIR__ . '/Configuration/common_config.php';
$pdo = include __DIR__ . '/Configuration/database_config.php';

$one_month = new DateInterval('P1M');
// $launch_date = (new DateTime('November 1, 2020'))->setTime(0,0);
$launch_date = (new DateTime('November 1, 2020'));
$offset_months = $argv[1] ?? 0;
$offset_di = new DateInterval('P'.$offset_months.'M');
$start_date = $launch_date->add($offset_di);

$filename = 'file://' . BASE_DIR . "/resource_stats_test_hourly.test.{$start_date->format("Y.m.d")}.json";

$handle = fopen($filename, "r");
$content = fgets($handle);

// Load importer class file
$importerClassName = "Importer_iOS";
include(IMPORTER_DIR . "/$importerClassName.php");
$importer = new $importerClassName($pdo, $filename);

// Load the data
$importer->import();
