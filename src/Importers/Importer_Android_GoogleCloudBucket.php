<?php

require_once __DIR__ . '/Importer_GoogleCloudBucket.php';
require_once MODEL_DIR . '/DataStoreModel_Android_GoogleCloudBucket.php';

class Importer_Android_GoogleCloudBucket extends Importer_GoogleCloudBucket {

    protected $sourceDateTimeFormat = "Y-m-d|";

    public function __construct(\PDO $pdo, $location, $authType=null, $authVal=null) {
        $this->model = new DataStoreModel_Android_GoogleCloudBucket($pdo);
        parent::__construct($this->model, $location, $authType, $authVal);
    }
}
