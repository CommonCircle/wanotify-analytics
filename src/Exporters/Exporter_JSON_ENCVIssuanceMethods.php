<?php

require_once __DIR__ . '/Exporter_JSON.php';
require_once MODEL_DIR . '/DataStoreModel_ENCVIssuanceMethods.php';


class Exporter_JSON_ENCVIssuanceMethods extends Exporter_JSON {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_ENCVIssuanceMethods($pdo);
        parent::__construct($model);
    }

    // Override to calculate user-uploaded bulk issuance
    protected function prepareData($data) {
        $preparedData = array(
            'ENCVIssuanceMethods' => array_map(function($e) {
                $first_day_bulk_api = '2022-07-29';
                if ($e['date'] < $first_day_bulk_api 
                    || ((date('N', strtotime($e['date'])) < 6) 
                    && isset($e['Max_User_Issuance']) 
                    && $e['Max_User_Issuance'] >= 25)) {
                    
                    $e['User_Bulk_Issuance'] = $e['Max_User_Issuance'];
                } else {
                    $e['User_Bulk_Issuance'] = 0;
                }
                $e['User_Single_Issuance'] = $e['Total_User_Issuance'] - $e['User_Bulk_Issuance'];
                return $e;
            }, parent::prepareData($data)),
        );
        return $preparedData;
    }
}

