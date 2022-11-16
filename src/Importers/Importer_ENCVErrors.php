<?php

require_once __DIR__ . '/Importer_ENCVStatsAPI.php';
require_once MODEL_DIR . '/DataStoreModel_ENCVErrors.php';

class Importer_ENCVErrors extends Importer_ENCVStatsAPI {

    public function __construct(\PDO $pdo, $location, $authType=null, $authVal=null) {
        $this->model = new DataStoreModel_ENCVErrors($pdo);
        parent::__construct($this->model, $location, $authType, $authVal);
    }

    protected function processData($data) : DatedData {
        $dataArray = $data['statistics'];
        $processedDataByDate = array();
        foreach ($dataArray as $d) {
            $date = $this->convertDate($this->sourceDateTimeFormat, $d['date']);
            $row = array();
            foreach ($d['error_data'] as $error_data) {
                $row["error_".$error_data['error_code']] = $error_data['quantity'];
            }
            if (array_sum($row)) {
                $processedDataByDate[$date] = $row;
            }
        }

        $processedData = new DatedData();
        foreach ($processedDataByDate as $date => $data) {
            $processedData->addData($date, $data);
        }
        return $processedData;
    }
}
