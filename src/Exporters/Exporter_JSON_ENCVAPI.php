<?php

require_once __DIR__ . '/Exporter_JSON.php';
require_once MODEL_DIR . '/DataStoreModel_ENCVAPI.php';


class Exporter_JSON_ENCVAPI extends Exporter_JSON {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_ENCVAPI($pdo);
        parent::__construct($model);
    }
}

