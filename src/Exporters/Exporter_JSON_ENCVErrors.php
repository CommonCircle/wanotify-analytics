<?php

require_once __DIR__ . '/Exporter_JSON.php';
require_once MODEL_DIR . '/DataStoreModel_ENCVErrors.php';


class Exporter_JSON_ENCVErrors extends Exporter_JSON {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_ENCVErrors($pdo);
        parent::__construct($model);
    }
}

