<?php

require_once __DIR__ . '/Exporter_GoogleSheets.php';
require_once MODEL_DIR . '/DataStoreModel_WebCounters.php';


class Exporter_GoogleSheets_WebCounters extends Exporter_GoogleSheets {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_WebCounters($pdo);
        parent::__construct($model);
    }
}

