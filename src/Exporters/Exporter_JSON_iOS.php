<?php

require_once __DIR__ . '/Exporter_JSON.php';
require_once MODEL_DIR . '/DataStoreModel_iOS.php';


class Exporter_JSON_iOS extends Exporter_JSON {
    
    public function __construct(\PDO $pdo) {
        $model = new DataStoreModel_iOS($pdo);
        parent::__construct($model);
    }

    // Override to add language totals
    // TODO calculate cumulative on import to improve performance and capture full date range
    protected function prepareData($data) {
        // Get regular iOS data
        $preparedData = array(
            'iOS' => parent::prepareData($data),
            'iOS_Languages' => array()
        );
        // Array of language field name => total
        $language_totals = array_fill_keys(
            array_filter(
                array_keys($preparedData['iOS'][0]),
                function ($k) {
                    return strpos($k, "_Settings") !== FALSE;
                }
            ),
            0
        );

        foreach ($preparedData['iOS'] as $data) {
            foreach (array_keys($language_totals) as $key) {
                $language_totals[$key] += $data[$key] ?? 0;
            }
        }

        foreach ($language_totals as $key => $total) {
            // Clean language string and build table
            $language = str_replace("_Settings", "", $key);
            if (strpos($language, "_") !== false) {
                // Change last word to language variation
                $language = preg_replace("/(.*)_(\w*)/", "$1 ($2)", $language);
                if (strpos($language, "_") !== false) {
                    // Change an additional first space to be "/"; only used for "Japanese/Chinese (Traditional)"
                    $language = preg_replace("/(.*)_(.*)/", "$1/$2", $language);
                }
            }
            $preparedData['iOS_Languages'][] = array('Language' => $language, 'Activations' => $total);
        }

        return $preparedData;
    }
}

