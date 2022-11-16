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

    /**
     * Override parent class function
     */
    protected function calculateSumFields($data) {
        $data['Total_EN_Page_Hits'] = 
            // Configuration pages pre-5/2/22
            ($data['/next-steps-count Mozilla'] ?? 0) 
            + ($data['/next-steps-count-30 Mozilla'] ?? 0) 
            + ($data['/next-steps-count-sr Mozilla'] ?? 0) 
            + ($data['/next-steps-count-advisory Mozilla'] ?? 0)
            // Configuration pages post-5/2/22
            + ($data['/next-steps-count-7.30 Mozilla'] ?? 0)
            + ($data['/next-steps-count-30.120 Mozilla'] ?? 0)
            + ($data['/next-steps-count-2.8 Mozilla'] ?? 0)
            + ($data['/next-steps-count-8.24 Mozilla'] ?? 0);

        $data['Total_Text_Page_Hits'] = ($data['/next-steps-text'] ?? 0) + ($data['/next-steps-text-sp'] ?? 0);
        return $data;
    }
}