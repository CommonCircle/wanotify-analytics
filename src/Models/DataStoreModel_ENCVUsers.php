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

    /**
     * Override parent class function
     */
    protected function calculateSumFields($data) {
        $data['Max_User_Issuance'] = 0;
        $data['Total_User_Issuance'] = 0;
        
        $max_issuance = max($data);
        $total_issuance = array_sum($data);

        $data['Max_User_Issuance'] = $max_issuance;
        $array_sans_max = array_diff($data, array($max_issuance));
        if ($array_sans_max) {
            $second_max = max($array_sans_max);
            if ($second_max >= 25) {
                $data['Max_User_Issuance'] += $second_max;
            }
        }

        $data['Total_User_Issuance'] = $total_issuance;
        
        return $data;
    }
}