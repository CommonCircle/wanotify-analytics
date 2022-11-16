<?php
require_once __DIR__ . '/DataStoreModel.php';

class DataStoreModel_ENCVIssuanceMethods extends DataStoreModel {
    protected $baseTableName = 'encv_issuance_method_data';
    protected $importInterval = 'daily';
    protected $intervalSuffixes = array(
        'daily' => 'P1D',
        'weekly' => 'P7D',
    );

    public function __construct($pdo) {
        parent::__construct($pdo);
    }
}