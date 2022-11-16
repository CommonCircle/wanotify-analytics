<?php
require_once __DIR__ . '/DataStoreModel.php';

class DataStoreModel_Matomo_Actions extends DataStoreModel {
    protected $baseTableName = 'matomo_action_data';
    protected $importInterval = 'daily';
    protected $intervalSuffixes = array(
        'daily' => 'P1D',
        'weekly' => 'P7D',
    );

    public function __construct($pdo) {
        parent::__construct($pdo);
    }

    protected function calculateSumFields($data) {
        $total_exposure_uv = 0;
        $total_exposure_v = 0;
        $total_exposure_h = 0;
        $total_count_uv = 0;
        $total_count_v = 0;
        $total_count_h = 0;

        // Aggregate all exposure and counter page statistics
        foreach ($data as $field => $value) {
            if (strpos($field, '_Exposure') !== false) { // Avoid visits to index page 'Exposure'
                if (strpos($field, 'uniq_visitors') !== false) {
                    $total_exposure_uv += $value;
                } else if (strpos($field, 'visits') !== false) {
                    $total_exposure_v += $value;
                } else if (strpos($field, 'hits') !== false) {
                    $total_exposure_h += $value;
                }
            } else if (strpos($field, 'Count') !== false) {
                if (strpos($field, 'uniq_visitors') !== false) {
                    $total_count_uv += $value;
                } else if (strpos($field, 'visits') !== false) {
                    $total_count_v += $value;
                } else if (strpos($field, 'hits') !== false) {
                    $total_count_h += $value;
                }
            }
        }

        $data['Total_Exposure_Unique_Visitors'] = $total_exposure_uv;
        $data['Total_Exposure_Visits'] = $total_exposure_v;
        $data['Total_Exposure_Hits'] = $total_exposure_h;
        $data['Total_Count_Unique_Visitors'] = $total_count_uv;
        $data['Total_Count_Visits'] = $total_count_v;
        $data['Total_Count_Hits'] = $total_count_h;

        return $data;
    }
}