<?php

require_once __DIR__ . '/Importer_WebLogs.php';
require_once MODEL_DIR . '/DataStoreModel_iOS.php';

class Importer_iOS extends Importer_WebLogs {

    public function __construct(\PDO $pdo, $location, $authType=null, $authVal=null) {
        $this->resourceNames = array(
            "iOS installs"
        );
        $this->onlyUserAgentInFieldName = true;
        $model = new DataStoreModel_iOS($pdo);
        parent::__construct($model, $location, $authType, $authVal);
    }
}
