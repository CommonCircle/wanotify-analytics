<?php

require_once __DIR__ . '/Exporter_GoogleSheets.php';
require_once MODEL_DIR . '/DataStoreModel_Android.php';


class Exporter_GoogleSheets_Android extends Exporter_GoogleSheets {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_Android($pdo);
        parent::__construct($model);
    }
}

