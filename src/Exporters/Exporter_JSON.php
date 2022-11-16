<?php
require_once __DIR__ . '/Exporter.php';

class Exporter_JSON extends Exporter {

    public function __construct($model) {
        $this->model = $model;
    }

    // Get the data from the data store and prepare it for the destination
    public function export($dataInterval, $startTime=null, $endTime=null) {
        $data = $this->model->selectDataByIntervalAndRange($dataInterval, $startTime, $endTime, $this->model::UNINCLUSIVE_UPPER);
        $data = $this->prepareData($data);
        return $data;
    }

    // Returns rows with date column followed by data columns
    // First row returned is complete list of field names
    protected function prepareData($data) {
        $dateFieldName = "date";
        $preparedData = array();
        foreach ($data as $row) {
            // add date field to data contents
            $preparedRow = array_merge(array($dateFieldName => $row['date']), $row['data']);
            $preparedData[] = $preparedRow;
        }
        
        return $preparedData;
    }
}