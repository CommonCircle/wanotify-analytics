<?php

require_once __DIR__ . '/Exporter_JSON.php';
require_once MODEL_DIR . '/DataStoreModel_Cases.php';


class Exporter_JSON_Cases extends Exporter_JSON {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_Cases($pdo);
        parent::__construct($model);
    }
}

