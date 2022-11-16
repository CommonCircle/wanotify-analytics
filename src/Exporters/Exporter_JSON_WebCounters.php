<?php

require_once __DIR__ . '/Exporter_JSON.php';
require_once MODEL_DIR . '/DataStoreModel_WebCounters.php';


class Exporter_JSON_WebCounters extends Exporter_JSON {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_WebCounters($pdo);
        parent::__construct($model);
    }
}

