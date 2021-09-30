<?php
require_once __DIR__ . '/DataStoreModel.php';

class DataStoreModel_Cases extends DataStoreModel {
    protected $baseTableName = 'case_data';
    protected $importInterval = 'daily';
    protected $intervalSuffixes = array(
        'daily' => 'P1D',
        'weekly' => 'P7D',
    );

    public function __construct($pdo) {
        parent::__construct($pdo);
    }

    // Synthetic fields added here
    protected function prepareData(DatedData $data) : DatedData {
        $datedData = $data->getData();
        foreach ($datedData as $date => $d) {
            $data->setData($date, $d);
        }
        return $data;
    }
}