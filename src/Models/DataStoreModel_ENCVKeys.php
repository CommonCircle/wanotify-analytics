<?php
require_once __DIR__ . '/DataStoreModel.php';

class DataStoreModel_ENCVKeys extends DataStoreModel {
    protected $baseTableName = 'encv_key_data';
    protected $importInterval = 'daily';
    protected $intervalSuffixes = array(
        'daily' => 'P1D',
        'weekly' => 'P7D',
    );

    public function __construct($pdo) {
        parent::__construct($pdo);
    }

    // Synthetic fields added here
    protected function prepareData(DatedData $datedData) : DatedData {
        $dd = $datedData->getData();
        foreach($dd as $date => $data) {
            $data['Total_Publish_Requests'] = $data['publish_requests_android'] + $data['publish_requests_ios'] + $data['publish_requests_unknown'];

            $datedData->setData($date, $data);
        }
        return $datedData;
    }
}