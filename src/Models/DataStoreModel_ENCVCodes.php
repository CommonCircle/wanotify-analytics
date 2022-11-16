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

    protected function calculateRateFields($data) {
        if ($data['codes_issued']) {
            $data['Codes_Claimed_per_Issued'] = $data['codes_claimed'] / $data['codes_issued'];
            $data['User_Issued_per_All_Issued'] = $data['user_reports_issued'] / $data['codes_issued'];
        }
        if ($data['codes_claimed']) {
            // TODO: rename to 'Tokens_per_Code_Claimed'
            $data['Tokens_Claimed_per_Code_Claimed'] = $data['tokens_claimed'] / $data['codes_claimed'];
        }
        if ($data['user_reports_issued']) {
            $data['User_Claimed_per_User_Issued'] = $data['user_reports_claimed'] / $data['user_reports_issued'];
        }
        if ($data['user_reports_claimed']) {
            $data['User_Tokens_per_User_Claimed'] = $data['user_report_tokens_claimed'] / $data['user_reports_claimed'];
        }
        return $data;
    }
}