<?php

require_once __DIR__ . '/Importer_ENCVStatsAPI.php';
require_once MODEL_DIR . '/DataStoreModel_ENCVAPI.php';

class Importer_ENCVAPI extends Importer_ENCVStatsAPI {

    public function __construct(\PDO $pdo, $location, $authType=null, $authVal=null) {
        $this->model = new DataStoreModel_ENCVAPI($pdo);
        parent::__construct($this->model, $location, $authType, $authVal);
    }

    protected function processData($data) : DatedData {
        $dataArray = $data['statistics'];
        
        $dates = array_column($dataArray, 'date');
        $minDateTime = $this->convertDate($this->ENCVDateTimeFormat, min($dates));
        $maxDateTime = $this->convertDate($this->ENCVDateTimeFormat, max($dates));
        
        $currentData = $this->model->selectDataIntervalByDate('daily', $minDateTime, $maxDateTime);
        $currentDataByDate = array_column($currentData, 'data', 'date');
        
        $processedDataByDate = array();
        foreach ($dataArray as $d) {
            $date = $this->convertDate($this->ENCVDateTimeFormat, $d['date']);
            $newData = array($data['authorized_app_name'] => $d['data']['codes_issued']);
            $updatedData = array_merge($currentDataByDate[$date] ?? array(), $newData);
            if (array_sum($updatedData) || $date == date_create()->format($this->dbDateTimeFormat)) {
                $processedDataByDate[$date] = $updatedData;
            }
        }

        $processedData = new DatedData();
        foreach ($processedDataByDate as $date => $data) {
            $processedData->setData($date, $data);
        }
        return $processedData;
    }
}
