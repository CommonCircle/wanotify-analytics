<?php

require_once __DIR__ . '/Exporter_JSON.php';
require_once MODEL_DIR . '/DataStoreModel_ENCVUsers.php';


class Exporter_JSON_ENCVUsers extends Exporter_JSON {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_ENCVUsers($pdo);
        parent::__construct($model);
    }

    // Override to remove individual issuance
    protected function prepareData($data) {
        $preparedData = array(
            'ENCVUsers' => array_map(function($e) {
                return array(
                    'date' => $e['date'], 
                    'Total_User_Issuance' => $e['Total_User_Issuance'], 
                    'Max_User_Issuance' => $e['Max_User_Issuance'],
                ); 
            }, parent::prepareData($data)),
        );
        return $preparedData;
    }
}