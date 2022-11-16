<?php
require_once __DIR__ . '/DataStoreModel.php';

class DataStoreModel_Matomo_Events extends DataStoreModel {
    protected $baseTableName = 'matomo_event_data';
    protected $importInterval = 'daily';
    protected $intervalSuffixes = array(
        'daily' => 'P1D',
        'weekly' => 'P7D',
    );

    public function __construct($pdo) {
        parent::__construct($pdo);
    }

    protected function calculateSumFields($data) {
        // Aggregate language selections
        $total_language_selections = 0;
        foreach ($data as $field => $value) {
            if (strpos($field, 'Select') === 0) {
                $total_language_selections += $value;
            }
        }
        $data['Total_Language_Selects'] = $total_language_selections;

        return $data;
    }
}