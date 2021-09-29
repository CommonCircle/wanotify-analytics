<?php

require_once __DIR__ . '/Exporter_GoogleSheets.php';
require_once MODEL_DIR . '/DataStoreModel_ENCVCodes.php';


class Exporter_GoogleSheets_ENCVCodes extends Exporter_GoogleSheets {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_ENCVCodes($pdo);
        parent::__construct($model);
    }
}

