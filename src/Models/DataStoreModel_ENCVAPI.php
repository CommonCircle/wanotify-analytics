<?php
require_once __DIR__ . '/DataStoreModel.php';

class DataStoreModel_ENCVAPI extends DataStoreModel {
    protected $baseTableName = 'encv_api_key_data';
    protected $importInterval = 'daily';
    protected $intervalSuffixes = array(
        'daily' => 'P1D',
        'weekly' => 'P7D',
    );

    public function __construct($pdo) {
        parent::__construct($pdo);
    }

    /**
     * Override parent class upsert function to merge with current data before writing
     */
    protected function upsert(DatedData $dataByDate, $interval=null) {
        return $this->mergeUpsert($dataByDate, $interval);
    }
}