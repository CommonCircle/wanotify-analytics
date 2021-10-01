<?php
require_once __DIR__ . '/DataStoreModel.php';

class DataStoreModel_ENCVUsers extends DataStoreModel {
    protected $baseTableName = 'encv_user_data';
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
            $data['Total_User_Issuance'] = array_sum($data);
            
            $datedData->setData($date, $data);
        }
        return $datedData;
    }
}