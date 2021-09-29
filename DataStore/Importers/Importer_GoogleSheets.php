<?php
require_once __DIR__ . "/Importer.php";
require_once VENDOR_DIR . '/autoload.php';

class Importer_GoogleSheets extends Importer {
    
    // URL of data or local file to cURL
    protected $sheetsInfo;

    // Credentials file
    protected $credentialsFile;

    // TODO handles token auth only
    public function __construct($model, $sheetsInfo, $authType=null, $authValue=null) {
        parent::__construct($model);
        $this->sheetsInfo = $sheetsInfo;
        $this->credentialsFile = $authValue;
    }

    public function getCurrentDatedData(): DatedData {

        // Set up sheets client
        $client = new \Google_Client();
        $client->setApplicationName('ENX Sheets Importer');
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');
        $client->setAuthConfig($this->credentialsFile);

        try {
            $service = new Google_Service_Sheets($client);
        } catch (Exception $e) {
            echo "Error creating service:\n";
            echo $e."\n";
            echo "Retrying...\n";
            sleep(20);
            $service = new Google_Service_Sheets($client);
        }

        try {
            $response = $service->spreadsheets_values->get($this->sheetsInfo['spreadsheet_id'], $this->sheetsInfo['sheet_name'] . '!A1:BZ');
            $contents = $response->getValues();
        } catch (Exception $e) {
            echo "Error getting current $dataTypeSuffix values:\n";
            echo $e."\n";
            echo "Retrying...\n";
            sleep(20);
            try {
                $response = $service->spreadsheets_values->get($spreadsheetId, $headerRange);
                $contents = $response->getValues();
            } catch (Exception $e) {
                echo("Failed.\n$e\n");
            }
        }

        $processedData = $this->processData($contents);
        
        return $processedData;
    }

    /**
     * input -> json/array
     * 
     * output ->
     * [fields => [ list of field names ],
     *  data => [ date => [ field => value, ... ], ... ] ]
     */
    protected function processData($data): DatedData {
        $dataArray = $data;
        // var_dump($dataArray);
        $fieldNames = array_shift($dataArray);
        // var_dump($fieldNames);
        // exit;
        $processedDataByDate = array();
        foreach ($dataArray as $d) {
            // Remove extra appended columns
            if (count($d) > count($fieldNames)) {
                $d = array_slice($d, 0, count($fieldNames));
            }
            // Remove empty internal columns from row
            $keyedData = array_filter(array_combine(array_slice($fieldNames,0,count($d)), $d), function ($a) { return $a !== ""; });

            $date = $keyedData['Date Time'];
            unset($keyedData['Date Time']);
            if (!empty($keyedData)) {
                $processedDataByDate[$date] = $keyedData;
            }
        }

        $processedData = new DatedData();
        foreach ($processedDataByDate as $date => $data) {
            $processedData->setData($date, $data);
        }
        return $processedData;
    }

}
