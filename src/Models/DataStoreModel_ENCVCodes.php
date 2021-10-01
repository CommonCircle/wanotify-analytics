<?php
require_once __DIR__ . '/DataStoreModel.php';

class DataStoreModel_ENCVCodes extends DataStoreModel {
    protected $baseTableName = 'encv_code_data';
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
            $data['Codes_Claimed_per_Issued'] = $data['codes_claimed'] / $data['codes_issued'];
            $data['Tokens_Claimed_per_Code_Claimed'] = $data['tokens_claimed'] / $data['codes_claimed'];
            
            $datedData->setData($date, $data);
        }
        return $datedData;
    }
}