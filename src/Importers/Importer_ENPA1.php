<?php

require_once __DIR__ . '/Importer_ENPA.php';
require_once MODEL_DIR . '/DataStoreModel_ENPA1.php';

class Importer_ENPA1 extends Importer_ENPA {

    public function __construct(\PDO $pdo, $location, $authType=null, $authVal=null) {
        $this->model = new DataStoreModel_ENPA1($pdo);
        parent::__construct($this->model, $location, $authType, $authVal);
    }
}
