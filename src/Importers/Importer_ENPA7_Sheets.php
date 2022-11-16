<?php

require_once __DIR__ . '/Importer_GoogleSheets.php';
require_once MODEL_DIR . '/DataStoreModel_ENPA7.php';

class Importer_ENPA7_Sheets extends Importer_GoogleSheets {

    public function __construct(\PDO $pdo, $location, $authType=null, $authVal=null) {
        $this->model = new DataStoreModel_ENPA7($pdo);
        parent::__construct($this->model, $location, $authType, $authVal);
    }
}