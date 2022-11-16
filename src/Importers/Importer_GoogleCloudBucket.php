<?php
require_once __DIR__ . "/Importer.php";
require_once VENDOR_DIR . '/autoload.php';

use Google\Cloud\Storage\StorageClient;

class Importer_GoogleCloudBucket extends Importer {
    
    // URL of data or local file to cURL
    protected $bucketPath;

    // Credentials file
    protected $credentialsFile;

    // TODO handles token auth only
    public function __construct($model, $bucketPath, $authType=null, $authValue=null) {
        parent::__construct($model);
        $this->bucketPath = $bucketPath;
        $this->credentialsFile = $authValue;
    }

    public function getCurrentDatedData(): DatedData {
        $storage = new StorageClient([
            'keyFilePath' => $this->credentialsFile,
        ]);
        $storage->registerStreamWrapper();

        $path = $this->bucketPath;
        $contents = file_get_contents($path);
        if (!$contents) {
            throw new Exception("Unable to open Android data file at $path");
            return null;
        }
        $contents = mb_convert_encoding($contents , 'UTF-8' , 'UTF-16LE');
        // var_dump($contents); exit;

        // Download to file method
        // $bucket = $storage->bucket('pubsite_prod_9139719146238594871');
        // $object = $bucket->object('stats/installs/installs_gov.wa.doh.exposurenotifications_202104_overview.csv');
        // $localFileName = 'android
        // $object->downloadToFile('stats_installs_installs_gov.wa.doh.exposurenotifications_202104_overview-1.csv');
        // $contents = file_get

        $contentsArray = array_map("str_getcsv", explode("\n", $contents));

        $processedData = $this->processData($contentsArray);
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
        array_pop($dataArray);
        $headers = array_shift($dataArray);
        $headers[0] = 'date'; // remove encoding byte
        $headers = array_map(function($e) {
            return str_replace(" ", "_", strtolower($e));
        }, $headers);
        $date_column = $headers[0];

        $data = array_map(function($e) use ($headers){
                    return array_combine($headers, $e);
                }, $dataArray);
        // var_dump($data);

        $processedDataByDate = array();
        foreach ($data as $d) {
            // var_dump($d);
            $date = $this->convertDate($this->sourceDateTimeFormat, $d['date']);
            // $date = date_create_from_format('Y-m-d', $d[$date_column])->format('Y-m-d G:i:s');
            $processedData = array_slice($d, 2); // remove date and package name from data
            $processedDataByDate[$date] = $processedData;
        }
        // var_dump($processedDataByDate);
        $processedData = new DatedData();
        foreach ($processedDataByDate as $date => $data) {
            $processedData->addData($date, $data);
        }
        return $processedData;
    }

}
