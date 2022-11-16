<?php

require_once __DIR__ . '/Exporter_GoogleSheets.php';
require_once MODEL_DIR . '/DataStoreModel_Matomo_Events.php';


class Exporter_GoogleSheets_Matomo_Events extends Exporter_GoogleSheets {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_Matomo_Events($pdo);
        parent::__construct($model);
    }
}

