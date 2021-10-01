<?php
require_once __DIR__ . '/DataStoreModel.php';

class DataStoreModel_WebCounters extends DataStoreModel {
    protected $baseTableName = 'web_counter_data';
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
            $data['Total_EN_Page_Hits'] = ($data['/next-steps-count Mozilla'] ?? 0) + ($data['/next-steps-count-30 Mozilla'] ?? 0);
            $data['Total_Text_Page_Hits'] = ($data['/next-steps-text'] ?? 0) + ($data['/next-steps-text-sp'] ?? 0);
            $datedData->setData($date, $data);
        }
        return $datedData;
    }
}