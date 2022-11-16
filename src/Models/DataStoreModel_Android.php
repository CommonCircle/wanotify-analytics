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
    protected $cumulativeFields = array(
        'Cumulative_Acquisition' => 'Device acquisition (All devices, All events, Per interval, Hourly): United States',
        'Cumulative_Loss' => 'Device loss (All devices, All events, Per interval, Hourly): United States',
        'Cumulative_Net' => 'Net_Activations',
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

    /**
     * Override parent class calculateSumFields function
     */
    protected function calculateSumFields($data) {
        $data['Net_Activations'] = (($data['Device acquisition (All devices, All events, Per interval, Hourly): United States'] ?? $data['install_events']) ?? 0)
                                - (($data['Device loss (All devices, All events, Per interval, Hourly): United States'] ?? $data['uninstall_events']) ?? 0);
        return $data;
    }
}