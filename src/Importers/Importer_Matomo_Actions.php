<?php

require_once __DIR__ . '/Importer_Matomo.php';
require_once MODEL_DIR . '/DataStoreModel_Matomo_Actions.php';

class Importer_Matomo_Actions extends Importer_Matomo {
    private $labelNameMap = array(
        "/next-steps-count" => "Standard_Count",
        "/next-steps-count-30" => "Extended_Count",
        "/next-steps-count-sr" => "SR_Count",
        "/next-steps-count-advisory" => "Advisory_Count",
        "/next-steps-count-7.30" => "7.30_Count",
        "/next-steps-count-30.120" => "30.120_Count",
        "/next-steps-count-2.8" => "2.8_Count",
        "/next-steps-count-8.24" => "8.24_Count",
        "/ExposureNotification/exposure/" => "Exposure",
        "/ExposureNotification/exposure/en" => "English_Exposure",
        "/ExposureNotification/exposure/es" => "Spanish_Exposure",
        "/ExposureNotification/exposure/zh-cn" => "Chinese_Simplified_Exposure",
        "/ExposureNotification/exposure/ja" => "Japanese_Exposure",
        "/ExposureNotification/exposure/zh-hk" => "Chinese_Traditional_Exposure",
        "/ExposureNotification/exposure/ko" => "Korean_Exposure",
        "/ExposureNotification/exposure/vi" => "Vietnamese_Exposure",
        "/ExposureNotification/exposure/ru" => "Russian_Exposure",
        "/ExposureNotification/exposure/fr" => "French_Exposure",
        "/ExposureNotification/exposure/ar" => "Arabic_Exposure",
        "/ExposureNotification/exposure/am" => "Amharic_Exposure",
        "/ExposureNotification/exposure/my" => "Burmese_Exposure",
        "/ExposureNotification/exposure/fa" => "Farsi_Exposure",
        "/ExposureNotification/exposure/de" => "German_Exposure",
        "/ExposureNotification/exposure/hi" => "Hindi_Exposure",
        "/ExposureNotification/exposure/km" => "Khmer_Exposure",
        "/ExposureNotification/exposure/pt" => "Portuguese_Exposure",
        "/ExposureNotification/exposure/pa" => "Punjabi_Exposure",
        "/ExposureNotification/exposure/ro" => "Romanian_Exposure",
        "/ExposureNotification/exposure/so" => "Somali_Exposure",
        "/ExposureNotification/exposure/sw" => "Swahili_Exposure",
        "/ExposureNotification/exposure/tl" => "Tagalog/Filipino_Exposure",
        "/ExposureNotification/exposure/ta" => "Tamil_Exposure",
        "/ExposureNotification/exposure/th" => "Thai_Exposure",
        "/ExposureNotification/exposure/tr" => "Turkish_Exposure",
        "/ExposureNotification/exposure/uk" => "Ukrainian_Exposure",
        "/ExposureNotification/exposure/ur" => "Urdu_Exposure",
    );

    private $fieldNameMap = array(
        "nb_uniq_visitors" => "uniq_visitors",
        "nb_visits" => "visits",
        "nb_hits" => "hits",
    );

    public function __construct(\PDO $pdo, $location, $authType=null, $authVal=null) {
        $this->model = new DataStoreModel_Matomo_Actions($pdo);
        parent::__construct($this->model, $location, $authType, $authVal);
    }

    protected function processData($data) : DatedData {
        $dataArray = $data;
        $processedDataByDate = array();
        foreach ($dataArray as $date => $d) {
            $date = $this->convertDate($this->sourceDateTimeFormat, $date);
            $actionData = array();
            foreach ($d as $action) {
                $page = null;
                // Filter out unused pages
                if (array_key_exists('label', $action)) {
                    $page = $this->labelNameMap[$action['label']] ?? null;
                }
                if ($page !== null) {
                    foreach ($this->fieldNameMap as $field => $alias) {
                        $fieldName = $page . "_" . $alias;
                        $actionData[$fieldName] = $action[$field] ?? 0;
                    }
                }
            }
            $processedDataByDate[$date] = $actionData;
        }

        $processedData = new DatedData();
        foreach ($processedDataByDate as $date => $data) {
            $processedData->addData($date, $data);
        }
        return $processedData;
    }
}
