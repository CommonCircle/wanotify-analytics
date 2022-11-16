<?php

require_once __DIR__ . '/Importer_Matomo.php';
require_once MODEL_DIR . '/DataStoreModel_Matomo_Events.php';

class Importer_Matomo_Events extends Importer_Matomo {

    public function __construct(\PDO $pdo, $location, $authType=null, $authVal=null) {
        $this->model = new DataStoreModel_Matomo_Events($pdo);
        parent::__construct($this->model, $location, $authType, $authVal);
    }

    protected function processData($data) : DatedData {
        $dataArray = $data;
        $processedDataByDate = array();
        foreach ($dataArray as $date => $d) {
            $eventData = array();
            $date = $this->convertDate($this->sourceDateTimeFormat, $date);
            foreach ($d as $event) {
                $removeTranslatedLanguage = preg_replace("/ - (.+?) - /", "_", $event['label']);
                $replaceSeparator = str_replace(" - ", "_", $removeTranslatedLanguage);
                $replaceSpaces = str_replace(" ", "_", $replaceSeparator);
                $fieldName = $replaceSpaces;
                $eventData[$fieldName] = $event['nb_uniq_visitors'];
            }
            $processedDataByDate[$date] = $eventData;
        }

        $processedData = new DatedData();
        foreach ($processedDataByDate as $date => $data) {
            $processedData->addData($date, $data);
        }
        return $processedData;
    }
}
