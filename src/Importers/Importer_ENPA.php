<?php
require_once __DIR__ . "/Importer_JsonFile.php";

abstract class Importer_ENPA extends Importer_JsonFile {

    protected $sourceDateTimeFormat = "Y-m-d|";

    # Run Bill's analysis and return the output as JSON
    protected function processData($data) : DatedData {
        $dataArray = $data;
        $processedDataByDate = array();
        $max_rollup = max(array_column($dataArray, 'days'));
        foreach ($dataArray as $d) {
            if ($d['days'] == $max_rollup) {
                $date = $this->convertDate($this->sourceDateTimeFormat, $d['date']);
                unset($d['date']);
                $processedDataByDate[$date] = $d;
            }
        }

        $processedData = new DatedData();
        foreach ($processedDataByDate as $date => $data) {
            $processedData->addData($date, $data);
        }
        return $processedData;
    }
}
