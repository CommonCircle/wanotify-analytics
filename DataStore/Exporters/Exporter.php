<?php

abstract class Exporter {
    // Model to use. Subclass determined by importer implementation
    /**
     * @var DataStoreModel
     */
    protected $model;

    abstract protected function prepareData($data);

    // Get the data from the data store and prepare it for the destination
    public function export($dataInterval, $startTime=null, $endTime=null) {
        $data = $this->model->selectDataIntervalByDate($dataInterval, $startTime, $endTime);
        $data = $this->prepareData($data);
        return $data;
    }
}