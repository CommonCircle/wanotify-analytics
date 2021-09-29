<?php

require_once __DIR__ . '/Importer_GoogleSheets.php';
require_once MODEL_DIR . '/DataStoreModel_Cases.php';

class Importer_Cases extends Importer_GoogleSheets {

    public function __construct(\PDO $pdo, $location, $authType=null, $authVal=null) {
        $this->model = new DataStoreModel_Cases($pdo);
        parent::__construct($this->model, $location, $authType, $authVal);
    }
}
