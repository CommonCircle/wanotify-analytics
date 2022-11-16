<?php
$pdo = include __DIR__ . '/Configuration/database_config.php';
$config = include __DIR__ . '/Configuration/generator_config.php';

foreach ($config->intervalTypes as $type) {
    // Load model class file
    // echo $type;
    $modelClassName = "DataStoreModel_$type";
    include(MODEL_DIR . "/$modelClassName.php");
    $model = new $modelClassName($pdo);

    $model->generateDataInterval('daily', '2020-11-30 00:00:00');
}

foreach ($config->cumulativeTypes as $type) {
    $modelClassName = "DataStoreModel_$type";
    $model = new $modelClassName($pdo);

    $model->generateCumulativeFields('daily');
}
