<?php
require_once __DIR__ . '/Exporter.php';

class Exporter_GoogleSheets extends Exporter {

    public function __construct($model) {
        $this->model = $model;
    }

    // Returns rows with date column followed by data columns
    // First row returned is complete list of field names
    protected function prepareData($data) {
        $dateFieldName = "Date Time";
        $fieldsTemp = array(); // list of all fields in data col
        $preparedData = array();
        foreach ($data as $row) {
            // add date field to data contents
            $preparedRow = array_merge(array($dateFieldName => $row['date']), $row['data']);
            $preparedData[] = $preparedRow;

            // build field list
            $fieldsTemp = array_merge($fieldsTemp, $preparedRow);
        }

        array_unshift($preparedData, array_keys($fieldsTemp));
        return $preparedData;
    }
}