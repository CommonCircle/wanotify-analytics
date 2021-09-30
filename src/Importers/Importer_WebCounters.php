<?php

require_once __DIR__ . '/Importer_JsonFile.php';
require_once MODEL_DIR . '/DataStoreModel_WebCounters.php';

class Importer_WebCounters extends Importer_JsonFile {

    public function __construct(\PDO $pdo, $location, $authType=null, $authVal=null) {
        $this->model = new DataStoreModel_WebCounters($pdo);
        parent::__construct($this->model, $location, $authType, $authVal);
    }

    protected function processData($data) : DatedData {
        $resourceNames = array("/next-steps-count", "/next-steps-text", "/next-steps-text-sp", "/next-steps-count-30");

        $startTime = new DateTime($data['start_time']);
        $endTime = new DateTime($data['end_time']);

        $processedDataByDate = array();

        $datetimes = $this->generateDateTimes($startTime, $endTime, new DateInterval($this->model->intervalString));

        foreach ($resourceNames as $resourceName) {
            $dataArray = $data['data'];
            $byUserAgent = array_key_exists('user_agents', $dataArray[$resourceName]);
            if ($byUserAgent) {
                $dataArray = $dataArray[$resourceName]['user_agents'];
                $fields = array_keys($dataArray);
            } else {
                $fields = array($resourceName);
            }

            foreach ($fields as $field) {
                $newField = $byUserAgent ? $resourceName . " " . $field : $field;
                foreach ($datetimes as $dt) {
                    $processedDataByDate[$dt][$newField] = $dataArray[$field][$dt]['200'] ?? 0;
                }
            }
        }

        $processedData = new DatedData();
        foreach ($processedDataByDate as $date => $data) {
            $processedData->setData($date, $data);
        }
        return $processedData;
    }

    private function generateDateTimes($timeStart, $timeFinish, $timeInterval) {
        $datetimes = array();
        $datetime = $timeStart;
        while ($datetime <= $timeFinish) {
            $datetimes[] = $datetime->format("Y-m-d G:00:00");
            $datetime->add($timeInterval);
        }
        return $datetimes;
    }
}
