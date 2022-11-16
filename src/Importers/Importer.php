<?php
require_once UTIL_DIR . '/DatedData.php';

abstract class Importer {
    // Model to use. Subclass determined by importer implementation
    /**
     * @var DataStoreModel
     */
    protected $model;
    protected $dbDateTimeFormat = 'Y-m-d H:i:s';

    /**
     * Get the current data from the source as a DatedData object
     * 
     * input -> json/array
     * 
     * output ->
     * [fields => [ list of field names ],
     *  data => [ date => [ field => value, ... ], ... ] ]
     */
    abstract protected function getCurrentDatedData() : DatedData;

    public function __construct(DataStoreModel $model) {
        $this->model = $model;
    }

    // Get the data from the source and send it to the model
    public function import() {
        $currentDatedData = $this->getCurrentDatedData();

        $this->model->save($currentDatedData);
        // TODO handle errors upserting data
        
        return;
    }

    // Convert date string from data format to db format
    protected function convertDate($format, $dateTime) {
        return date_create_from_format($format, $dateTime)->format($this->dbDateTimeFormat);
    }
}
