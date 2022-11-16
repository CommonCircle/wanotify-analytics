<?php

require_once __DIR__ . '/Exporter_JSON.php';
require_once MODEL_DIR . '/DataStoreModel_LandingPage.php';


class Exporter_JSON_LandingPage extends Exporter_JSON {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_LandingPage($pdo);
        parent::__construct($model);
    }
}

