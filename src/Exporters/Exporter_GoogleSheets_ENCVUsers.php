<?php

require_once __DIR__ . '/Exporter_GoogleSheets.php';
require_once MODEL_DIR . '/DataStoreModel_ENCVUsers.php';


class Exporter_GoogleSheets_ENCVUsers extends Exporter_GoogleSheets {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_ENCVUsers($pdo);
        parent::__construct($model);
    }
}

