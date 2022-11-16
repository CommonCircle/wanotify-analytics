<?php

require_once __DIR__ . '/Exporter_JSON.php';
require_once MODEL_DIR . '/DataStoreModel_Android.php';


class Exporter_JSON_Android extends Exporter_JSON {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_Android($pdo);
        parent::__construct($model);
    }
}

