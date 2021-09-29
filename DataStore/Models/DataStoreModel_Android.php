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
    protected function prepareData(DatedData $data) : DatedData {
        $datedData = $data->getData();
        foreach ($datedData as $date => $d) {
            $data->setData($date, $d);
        }
        return $data;
    }
}