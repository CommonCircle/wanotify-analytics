<?php

require_once __DIR__ . '/Importer_JsonFile.php';

class Importer_WebLogs extends Importer_JsonFile {

    protected $resourceNames;
    protected $onlyUserAgentInFieldName;

    public function __construct($model, $location, $authType=null, $authVal=null) {
        parent::__construct($model, $location, $authType, $authVal);
    }

    protected function processData($data) : DatedData {
    
        $startTime = new DateTime($data['start_time']);
        $endTime = new DateTime($data['end_time']);

        $processedDataByDate = array();

        $datetimes = $this->generateDateTimes($startTime, $endTime, new DateInterval($this->model->importIntervalString));

        foreach ($this->resourceNames as $resourceName) {
            $dataArray = $data['data'];
            if (array_key_exists($resourceName, $dataArray)) {
                $byUserAgent = array_key_exists('user_agents', $dataArray[$resourceName]);
                if ($byUserAgent) {
                    $dataArray = $dataArray[$resourceName]['user_agents'];
                    // Fields are user agent strings
                    $fields = array_keys($dataArray);
                } else {
                    // Fields are resource names
                    $fields = array($resourceName);
                }

                foreach ($fields as $field) {
                    $newField = ($byUserAgent && !$this->onlyUserAgentInFieldName) ? $resourceName . " " . $field : $field;
                    foreach ($datetimes as $dt) {
                        $value = $dataArray[$field][$dt]['200'] ?? 0;
                        $processedDataByDate[$dt][$newField] = $value;
                        if ($resourceName == "iOS installs") {
                            $value304 = $dataArray[$field][$dt]['304'] ?? 0;
                            $processedDataByDate[$dt][$newField . '_304'] = $value;
                        }
                    }
                }
            }
        }

        $processedData = new DatedData();
        foreach ($processedDataByDate as $date => $data) {
            $processedData->addData($date, $data);
        }
        return $processedData;
    }

    protected function generateDateTimes($timeStart, $timeFinish, $timeInterval) {
        $datetimes = array();
        $datetime = $timeStart;
        while ($datetime <= $timeFinish) {
            $datetimes[] = $datetime->format("Y-m-d G:00:00");
            $datetime->add($timeInterval);
        }
        return $datetimes;
    }
}
