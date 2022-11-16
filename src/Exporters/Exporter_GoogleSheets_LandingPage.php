<?php

require_once __DIR__ . '/Exporter_GoogleSheets.php';
require_once MODEL_DIR . '/DataStoreModel_LandingPage.php';


class Exporter_GoogleSheets_LandingPage extends Exporter_GoogleSheets {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_LandingPage($pdo);
        parent::__construct($model);
    }
}

