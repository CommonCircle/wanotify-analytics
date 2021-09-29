<?php

require_once __DIR__ . '/Importer_ENCVStatsAPI.php';
require_once MODEL_DIR . '/DataStoreModel_ENCVCodes.php';

class Importer_ENCVCodes extends Importer_ENCVStatsAPI {

    public function __construct(\PDO $pdo, $location, $authType=null, $authVal=null) {
        $this->model = new DataStoreModel_ENCVCodes($pdo);
        parent::__construct($this->model, $location, $authType, $authVal);
    }

    protected function processData($data) : DatedData {
        $dataArray = $data['statistics'];
        $processedDataByDate = array();
        foreach ($dataArray as $d) {
            $date = $this->convertDate($this->ENCVDateTimeFormat, $d['date']);
            $row = array();
            foreach ($d['data'] as $field => $value) {
                if (is_array($value)) {
                    foreach ($value as $idx => $subvalue) {
                        $fieldName = $field . "_" . $idx;
                        $row[$fieldName] = $subvalue;
                    }
                } else {
                    $row[$field] = $value;
                }
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
