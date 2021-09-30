<?php
require_once __DIR__ . "/Importer.php";

abstract class Importer_JsonFile extends Importer {
    
    // URL of data or local file to cURL
    protected $dataAddress;

    // Headers for data download
    protected $downloadHeaders = array(
        "accept: application/json",
        "content-type: application/json"
    );

    /**
     * input -> json/array
     * 
     * output ->
     * [fields => [ list of field names ],
     *  data => [ date => [ field => value, ... ], ... ] ]
     */
    abstract protected function processData($data): DatedData;

    // TODO handles token auth only
    public function __construct($model, $location, $authType=null, $authVal=null) {
        parent::__construct($model);
        $this->dataAddress = $location;
        if ($authType && $authVal) {
            $this->downloadHeaders[] = "$authType: $authVal";
        }
    }

    public function getCurrentDatedData(): DatedData {
        $ch = curl_init();
        if (!$ch) {
            die("Couldn't initialize a cURL handle");
        }
        curl_setopt($ch, CURLOPT_URL, $this->dataAddress);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_USERAGENT,'cURL');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->downloadHeaders);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        // Check HTTP status code
        if (!curl_errno($ch)) {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_code >= 400) {
                echo 'Unexpected HTTP code: ', $http_code, "\n";
            }
        } else {
            echo 'Request error occurred: ', curl_errno($ch), "\n";
        }
        $contents = json_decode($response, true);
        curl_close($ch);

        $processedData = $this->processData($contents);
    
        return $processedData;
    }
}
