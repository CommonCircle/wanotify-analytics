<?php

require_once __DIR__ . '/Exporter_JSON.php';
require_once MODEL_DIR . '/DataStoreModel_ENCVCodes.php';


class Exporter_JSON_ENCVCodes extends Exporter_JSON {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_ENCVCodes($pdo);
        parent::__construct($model);
    }
}

