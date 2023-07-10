<?php

require_once __DIR__ . '/Importer_WebLogs.php';
require_once MODEL_DIR . '/DataStoreModel_WebCounters.php';

class Importer_WebCounters extends Importer_WebLogs {

    public function __construct(\PDO $pdo, $location, $authType=null, $authVal=null) {
        $this->resourceNames = array(
            "/next-steps-count",
            "/next-steps-text",
            "/next-steps-text-sp",
            "/next-steps-count-30",
            "/next-steps-count-sr",
            "/next-steps-count-advisory",
            "/next-steps-count-7.30",
            "/next-steps-count-30.120",
            "/next-steps-count-2.8",
            "/next-steps-count-8.24",
            "/sunset",
        );
        $model = new DataStoreModel_WebCounters($pdo);
        parent::__construct($model, $location, $authType, $authVal);
    }
}
