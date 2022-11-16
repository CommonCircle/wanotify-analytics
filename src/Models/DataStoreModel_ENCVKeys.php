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

    /**
     * Override parent class function
     */
    protected function calculateSumFields($data) {
        $data['Total_Publish_Requests'] = $data['publish_requests_android'] + $data['publish_requests_ios'] + $data['publish_requests_unknown'];
        return $data;
    }

    protected function calculateRateFields($data) {
        if ($data['Total_Publish_Requests']) {
            $data['Average_Days_Onset_to_Upload'] = ((
                $data['onset_to_upload_distribution_0'] * 1
                + $data['onset_to_upload_distribution_1'] * 2
                + $data['onset_to_upload_distribution_2'] * 3
                + $data['onset_to_upload_distribution_3'] * 4
                + $data['onset_to_upload_distribution_4'] * 5
                + $data['onset_to_upload_distribution_5'] * 6
                + $data['onset_to_upload_distribution_6'] * 7
                + $data['onset_to_upload_distribution_7'] * 8
                + $data['onset_to_upload_distribution_8'] * 9
                + $data['onset_to_upload_distribution_9'] * 10
                + $data['onset_to_upload_distribution_10'] * 11
                + $data['onset_to_upload_distribution_11'] * 12
                + $data['onset_to_upload_distribution_12'] * 13
                + $data['onset_to_upload_distribution_13'] * 14
                + $data['onset_to_upload_distribution_14'] * 15
                + $data['onset_to_upload_distribution_15'] * 16
                + $data['onset_to_upload_distribution_16'] * 17
                + $data['onset_to_upload_distribution_17'] * 18
                + $data['onset_to_upload_distribution_18'] * 19
                + $data['onset_to_upload_distribution_19'] * 20
                + $data['onset_to_upload_distribution_20'] * 21
                + $data['onset_to_upload_distribution_21'] * 22
                + $data['onset_to_upload_distribution_22'] * 23
                + $data['onset_to_upload_distribution_23'] * 24
                + $data['onset_to_upload_distribution_24'] * 25
                + $data['onset_to_upload_distribution_25'] * 26
                + $data['onset_to_upload_distribution_26'] * 27
                + $data['onset_to_upload_distribution_27'] * 28
                + $data['onset_to_upload_distribution_28'] * 29
                + $data['onset_to_upload_distribution_29'] * 30
            ) / $data['Total_Publish_Requests']) - 1;
        }
        return $data;
    }
}