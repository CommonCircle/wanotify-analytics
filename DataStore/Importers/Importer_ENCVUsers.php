<?php

require_once __DIR__ . '/Importer_ENCVStatsAPI.php';
require_once MODEL_DIR . '/DataStoreModel_ENCVUsers.php';

class Importer_ENCVUsers extends Importer_ENCVStatsAPI {

    public function __construct(\PDO $pdo, $location, $authType=null, $authVal=null) {
        $this->model = new DataStoreModel_ENCVUsers($pdo);
        parent::__construct($this->model, $location, $authType, $authVal);
    }

    protected function processData($data) : DatedData {
        $dataArray = $data['statistics'];
        $processedDataByDate = array();
        foreach ($dataArray as $d) {
            $date = $this->convertDate($this->ENCVDateTimeFormat, $d['date']);
            $row = array();
            foreach ($d['issuer_data'] as $issuer) {
                $fieldName = "{$issuer['name']} ({$issuer['email']})";
                $row[$fieldName] = $issuer['codes_issued'];
            }
            if (array_sum($row)) {
                $processedDataByDate[$date] = $row;
            }
        }

        $processedData = new DatedData();
        foreach ($processedDataByDate as $date => $data) {
            $processedData->setData($date, $data);
        }
        return $processedData;
    }
}
