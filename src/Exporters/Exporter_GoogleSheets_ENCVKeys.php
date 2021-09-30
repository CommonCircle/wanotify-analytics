<?php

require_once __DIR__ . '/Exporter_GoogleSheets.php';
require_once MODEL_DIR . '/DataStoreModel_ENCVKeys.php';


class Exporter_GoogleSheets_ENCVKeys extends Exporter_GoogleSheets {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_ENCVKeys($pdo);
        parent::__construct($model);
    }
}

