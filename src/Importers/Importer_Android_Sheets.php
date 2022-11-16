<?php

require_once __DIR__ . '/Importer_GoogleSheets.php';
require_once MODEL_DIR . '/DataStoreModel_Android.php';

class Importer_Android_Sheets extends Importer_GoogleSheets {

    public function __construct(\PDO $pdo, $location, $authType=null, $authVal=null) {
        $this->model = new DataStoreModel_Android($pdo);
        parent::__construct($this->model, $location, $authType, $authVal);
    }
}
