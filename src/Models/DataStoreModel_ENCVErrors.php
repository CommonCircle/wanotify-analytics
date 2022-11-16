<?php
require_once __DIR__ . '/DataStoreModel.php';

class DataStoreModel_ENCVErrors extends DataStoreModel {
    protected $baseTableName = 'encv_error_data';
    protected $importInterval = 'daily';
    protected $intervalSuffixes = array(
        'daily' => 'P1D',
        'weekly' => 'P7D',
    );

    public function __construct($pdo) {
        parent::__construct($pdo);
    }

    /**
     * Override parent class function
     */
    protected function calculateSumFields($data) {
        $data['Total_Errors'] = array_sum($data);
        return $data;
    }
}