<?php

require_once __DIR__ . '/Exporter_JSON.php';
require_once MODEL_DIR . '/DataStoreModel_ENPA1.php';


class Exporter_JSON_ENPA1 extends Exporter_JSON {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_ENPA1($pdo);
        parent::__construct($model);
    }
}

