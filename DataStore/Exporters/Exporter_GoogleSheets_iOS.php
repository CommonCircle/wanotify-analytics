<?php

require_once __DIR__ . '/Exporter_GoogleSheets.php';
require_once MODEL_DIR . '/DataStoreModel_iOS.php';


class Exporter_GoogleSheets_iOS extends Exporter_GoogleSheets {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_iOS($pdo);
        parent::__construct($model);
    }
}

