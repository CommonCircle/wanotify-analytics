<?php

require_once __DIR__ . '/Importer_GoogleSheets.php';
require_once MODEL_DIR . '/DataStoreModel_ENCVBulk.php';

class Importer_ENCVBulk extends Importer_GoogleSheets {

    public function __construct(\PDO $pdo, $location, $authType=null, $authVal=null) {
        $this->model = new DataStoreModel_ENCVBulk($pdo);
        parent::__construct($this->model, $location, $authType, $authVal);
    }
}
