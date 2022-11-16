<?php
include __DIR__ . '/Configuration/common_config.php';
$pdo = include __DIR__ . '/Configuration/database_config.php';
require_once UTIL_DIR . '/DatedData.php';

$typesToRefresh = array(
    // 'Android',
    // 'Cases',
    // 'ENCVAPI',
    // 'ENCVBulk',
    // 'ENCVCodes',
    // 'ENCVKeys',
    // 'ENCVErrors',
    // 'ENCVUsers',
    // 'iOS',
    // 'WebCounters',
);

foreach ($typesToRefresh as $type) { // TODO support multiple files
    // Load model class file
    $modelClassName = "DataStoreModel_$type";
    include(MODEL_DIR . "/$modelClassName.php");
    $model = new $modelClassName($pdo);

    $model->refreshData();

}
