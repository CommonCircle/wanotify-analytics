<?php
require_once __DIR__ . '/DataStoreModel.php';

class DataStoreModel_Android extends DataStoreModel {
    protected $baseTableName = 'android_data';
    protected $importInterval = 'hourly';
    protected $intervalSuffixes = array(
        'hourly' => 'PT1H',
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
            $data['Net_Activations'] =
                $data['Device acquisition (All devices, All events, Per interval, Hourly): United States'] ?? 0 +
                $data['Device loss (All devices, All events, Per interval, Hourly): United States'] ?? 0;
            $datedData->setData($date, $data);
        }
        return $datedData;
    }
}