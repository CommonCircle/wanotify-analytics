<?php

require_once __DIR__ . '/Exporter_GoogleSheets.php';
require_once MODEL_DIR . '/DataStoreModel_ENCVAPI.php';


class Exporter_GoogleSheets_ENCVAPI extends Exporter_GoogleSheets {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_ENCVAPI($pdo);
        parent::__construct($model);
    }
}

