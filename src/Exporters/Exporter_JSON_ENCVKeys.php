<?php

require_once __DIR__ . '/Exporter_JSON.php';
require_once MODEL_DIR . '/DataStoreModel_ENCVKeys.php';


class Exporter_JSON_ENCVKeys extends Exporter_JSON {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_ENCVKeys($pdo);
        parent::__construct($model);
    }
}

