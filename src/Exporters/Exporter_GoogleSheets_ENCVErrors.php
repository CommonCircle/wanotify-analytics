<?php

require_once __DIR__ . '/Exporter_GoogleSheets.php';
require_once MODEL_DIR . '/DataStoreModel_ENCVErrors.php';


class Exporter_GoogleSheets_ENCVErrors extends Exporter_GoogleSheets {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_ENCVErrors($pdo);
        parent::__construct($model);
    }
}

