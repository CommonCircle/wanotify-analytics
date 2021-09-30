<?php

require_once __DIR__ . '/Importer_ENCVStatsAPI.php';
require_once MODEL_DIR . '/DataStoreModel_ENCVKeys.php';

class Importer_ENCVKeys extends Importer_ENCVStatsAPI {

    public function __construct(\PDO $pdo, $location, $authType=null, $authVal=null) {
        $this->model = new DataStoreModel_ENCVKeys($pdo);
        parent::__construct($this->model, $location, $authType, $authVal);
    }

    protected function processData($data) : DatedData {
        $dataArray = $data;
        $processedDataByDate = array();
        foreach ($dataArray as $d) {
            $date = $this->convertDate($this->ENCVDateTimeFormat, $d['day']);
            foreach ($d as $field => $value) {
                if ($field != 'day') {
                    if (is_array($value)) {
                        foreach ($value as $idx => $subvalue) {
                            $fieldName = $field . "_" . $idx;
                            $processedDataByDate[$date][$fieldName] = $subvalue;
                        }
                    } else {
                        $fieldName = $field;
                        $processedDataByDate[$date][$fieldName] = $value;
                    }
                }
            }
        }

        $processedData = new DatedData();
        foreach ($processedDataByDate as $date => $data) {
            $processedData->setData($date, $data);
        }
        return $processedData;
    }
}
