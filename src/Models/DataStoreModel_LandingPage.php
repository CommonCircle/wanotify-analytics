<?php
require_once __DIR__ . '/DataStoreModel.php';

class DataStoreModel_LandingPage extends DataStoreModel {
    protected $baseTableName = 'landing_page_data';
    protected $importInterval = 'hourly';
    protected $intervalSuffixes = array(
        'hourly' => 'PT1H',
        'daily' => 'P1D',
        'weekly' => 'P7D',
    );

    private $fieldNameMap = array(
        "/ExposureNotification/exposure/ Mozilla" => "Exposure",
        "/ExposureNotification/exposure/en Mozilla" => "English_Exposure",
        "/ExposureNotification/exposure/es Mozilla" => "Spanish_Exposure",
        "/ExposureNotification/exposure/zh-cn Mozilla" => "Chinese_Simplified_Exposure",
        "/ExposureNotification/exposure/ja Mozilla" => "Japanese_Exposure",
        "/ExposureNotification/exposure/zh-hk Mozilla" => "Chinese_Traditional_Exposure",
        "/ExposureNotification/exposure/ko Mozilla" => "Korean_Exposure",
        "/ExposureNotification/exposure/vi Mozilla" => "Vietnamese_Exposure",
        "/ExposureNotification/exposure/ru Mozilla" => "Russian_Exposure",
        "/ExposureNotification/exposure/fr Mozilla" => "French_Exposure",
        "/ExposureNotification/exposure/ar Mozilla" => "Arabic_Exposure",
        "/ExposureNotification/exposure/am Mozilla" => "Amharic_Exposure",
        "/ExposureNotification/exposure/my Mozilla" => "Burmese_Exposure",
        "/ExposureNotification/exposure/fa Mozilla" => "Farsi_Exposure",
        "/ExposureNotification/exposure/de Mozilla" => "German_Exposure",
        "/ExposureNotification/exposure/hi Mozilla" => "Hindi_Exposure",
        "/ExposureNotification/exposure/km Mozilla" => "Khmer_Exposure",
        "/ExposureNotification/exposure/pt Mozilla" => "Portuguese_Exposure",
        "/ExposureNotification/exposure/pa Mozilla" => "Punjabi_Exposure",
        "/ExposureNotification/exposure/ro Mozilla" => "Romanian_Exposure",
        "/ExposureNotification/exposure/so Mozilla" => "Somali_Exposure",
        "/ExposureNotification/exposure/sw Mozilla" => "Swahili_Exposure",
        "/ExposureNotification/exposure/tl Mozilla" => "Tagalog/Filipino_Exposure",
        "/ExposureNotification/exposure/ta Mozilla" => "Tamil_Exposure",
        "/ExposureNotification/exposure/th Mozilla" => "Thai_Exposure",
        "/ExposureNotification/exposure/tr Mozilla" => "Turkish_Exposure",
        "/ExposureNotification/exposure/uk Mozilla" => "Ukrainian_Exposure",
        "/ExposureNotification/exposure/ur Mozilla" => "Urdu_Exposure",
    );

    public function __construct($pdo) {
        parent::__construct($pdo);
    }

    // Synthetic fields added here
    protected function prepareData(DatedData $datedData) : DatedData {
        $dd = $datedData->getData();
        foreach ($dd as $date => $data) {
            // replace raw fieldnames with legible ones
            $data = $this->renameFields($data);
            $data = $this->generateCalculatedFields($data);
            $datedData->addData($date, $data);
        }
        return $datedData;
    }

    /**
     * Override parent class function
     */
    protected function calculateSumFields($data) {
        $data['Total_Landing_Page_Hits'] = 0;
        foreach (array_values($this->fieldNameMap) as $page) {
            if ($page != "Exposure") {
                $data['Total_Landing_Page_Hits'] += ($data[$page] ?? 0);
            }
        }
        return $data;
    }

    private function renameFields($data) {
        $rekeyedData = array();
        foreach ($data as $k => $d) {
            $rekeyedData[$this->fieldNameMap[$k] ?? $k] = $d;
        }
        return $rekeyedData;
    }
}