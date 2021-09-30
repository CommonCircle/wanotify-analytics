<?php

require_once __DIR__ . '/Importer_JsonFile.php';
require_once MODEL_DIR . '/DataStoreModel_iOS.php';

class Importer_iOS extends Importer_JsonFile {

    public function __construct(\PDO $pdo, $location, $authType=null, $authVal=null) {
        $this->model = new DataStoreModel_iOS($pdo);
        parent::__construct($this->model, $location, $authType, $authVal);
    }

    // TODO move 'Settings Total' field calculation into DataStoreModel_iOS
    protected function processData($data) : DatedData {
        $resourceName = "iOS installs";
        $nonSettings = array("ENBuddy", "HealthENBuddy");
        $allSettings = "Settings Total";

        $dataArray = $data['data'];
        if (array_key_exists('user_agents', $data['data'][$resourceName])) {
            $dataArray = $dataArray[$resourceName]['user_agents'];
        }

        $startTime = new DateTime($data['start_time']);
        $endTime = new DateTime($data['end_time']);

        $processedDataByDate = array();

        // get all user agents
        $fields = array_keys($dataArray);

        $datetimes = $this->generateDateTimes($startTime, $endTime, new DateInterval($this->model->intervalString));

        foreach ($fields as $field) {
            foreach ($datetimes as $dt) {
                $processedDataByDate[$dt][$field] = $dataArray[$field][$dt]['200'] ?? 0;
            
                // TODO: loop through all generated fields for this 
                // Ensure settings agg field exists
                if (!array_key_exists($allSettings, $processedDataByDate[$dt])) {
                    $processedDataByDate[$dt][$allSettings] = 0;
                }
                // Add settings field totals
                if (!(in_array($field, $nonSettings) || preg_match("/[^a-zA-Z\d%]/", $field))) {
                    $processedDataByDate[$dt][$allSettings] += $processedDataByDate[$dt][$field];
                }

                // TODO: more generated fields?
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
