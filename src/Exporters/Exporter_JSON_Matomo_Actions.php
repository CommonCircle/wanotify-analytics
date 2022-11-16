<?php

require_once __DIR__ . '/Exporter_JSON.php';
require_once MODEL_DIR . '/DataStoreModel_Matomo_Actions.php';


class Exporter_JSON_Matomo_Actions extends Exporter_JSON {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_Matomo_Actions($pdo);
        parent::__construct($model);
    }
}

