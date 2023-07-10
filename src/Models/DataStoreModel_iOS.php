<?php
require_once __DIR__ . '/DataStoreModel.php';

class DataStoreModel_iOS extends DataStoreModel {
    protected $baseTableName = 'ios_data';
    protected $importInterval = 'hourly';
    protected $intervalSuffixes = array(
        'hourly' => 'PT1H',
        'daily' => 'P1D',
        'weekly' => 'P7D',
    );
    protected $cumulativeFields = array(
        'Cumulative_Total' => 'Total_Adjusted',
    );

    private $fieldNameMap = array(
        'Settings'                                                                                   => 'English_Settings',
        'Configuraci%C3%B3n'                                                                         => 'Spanish_US_Settings',
        '%E8%AE%BE%E7%BD%AE'                                                                         => 'Chinese_Simplified_Settings',
        '%E8%A8%AD%E5%AE%9A'                                                                         => 'Japanese_Chinese_Traditional_Settings',
        '%EC%84%A4%EC%A0%95'                                                                         => 'Korean_Settings',
        'C%C3%A0i%20%C4%91%E1%BA%B7t'                                                                => 'Vietnamese_Settings',
        '%D0%9D%D0%B0%D1%81%D1%82%D1%80%D0%BE%D0%B9%D0%BA%D0%B8'                                     => 'Russian_Settings',
        'R%C3%A9glages'                                                                              => 'French_Settings',
        'Ajustes'                                                                                    => 'Spanish_ES_Settings',
        '%D8%A7%D9%84%D8%A5%D8%B9%D8%AF%D8%A7%D8%AF%D8%A7%D8%AA'                                     => 'Arabic_Settings',
        'Ayarlar'                                                                                    => 'Turkish_Settings',
        'Einstellungen'                                                                              => 'German_Settings',
        '%D0%9F%D0%B0%D1%80%D0%B0%D0%BC%D0%B5%D1%82%D1%80%D0%B8'                                     => 'Ukrainian_Settings',
        'Impostazioni'                                                                               => 'Italian_Settings',
        'Pengaturan'                                                                                 => 'Indonesian_Settings',
        '%D7%94%D7%92%D7%93%D7%A8%D7%95%D7%AA'                                                       => 'Hebrew_Settings',
        'Inst%C3%A4llningar'                                                                         => 'Swedish_Settings',
        'Instellingen'                                                                               => 'Dutch_Settings',
        'Configur%C4%83ri'                                                                           => 'Romanian_Settings',
        'Ustawienia'                                                                                 => 'Polish_Settings',
        'Be%C3%A1ll%C3%ADt%C3%A1sok'                                                                 => 'Hungarian_Settings',
        'Innstillinger'                                                                              => 'Norwegian_Settings',
        'Asetukset'                                                                                  => 'Finnish_Settings',
        'Defini%C3%A7%C3%B5es'                                                                       => 'Portugese_Settings',
        'Indstillinger'                                                                              => 'Danish_Settings',
        'Nastavenia'                                                                                 => 'Slovak_Settings',
        'Nastaven%C3%AD'                                                                             => 'Czech_Settings',
        '%CE%A1%CF%85%CE%B8%CE%BC%CE%AF%CF%83%CE%B5%CE%B9%CF%82'                                     => 'Greek_Settings',
        'Postavke'                                                                                   => 'Slovenian_Settings',
        'Configuraci%C3%B3'                                                                          => 'Catalan_Settings',
        'Seting'                                                                                     => 'Malay_Settings',
        '%E0%A4%B8%E0%A5%87%E0%A4%9F%E0%A4%BF%E0%A4%82%E0%A4%97%E0%A5%8D%E0%A4%9C%E0%A4%BC'          => 'Hindi_Settings',
        '%E0%B8%81%E0%B8%B2%E0%B8%A3%E0%B8%95%E0%B8%B1%E0%B9%89%E0%B8%87%E0%B8%84%E0%B9%88%E0%B8%B2' => 'Thai_Settings',
    );

    private $iosFirstDayRetentionRate = .80;
    private $iosRetentionRate = .75;

    public function __construct($pdo) {
        parent::__construct($pdo);
    }

    // Synthetic fields added here
    protected function prepareData(DatedData $datedData) : DatedData {
        $dd = $datedData->getData();
        foreach ($dd as $date => $data) {
            // replace raw fieldnames with human-readable ones
            $data = $this->renameFields($data);
            // add the settings field values
            $data['Settings_Total'] = $this->sumSettingsFields($data);
            $data = $this->generateCalculatedFields($data);
            $datedData->addData($date, $data);
        }
        return $datedData;
    }

    protected function calculateSumFields($data) {
        // set the proper ios retention rate
        // $rate = ($date < '2020-12-01' ? $this->iosFirstDayRetentionRate : $this->iosRetentionRate);
        $rate = $this->iosRetentionRate;
        // create the final adjusted sum fields using the rate
        $data['Settings_Adjusted'] = ($data['Settings_Total'] ?? 0) * $rate;
        $data['HealthENBuddy_Adjusted'] = ($data['HealthENBuddy'] ?? 0) * $rate;
        $data['ENBuddy_Adjusted'] = ($data['ENBuddy'] ?? 0) * $rate;
        $data['Total_Adjusted'] = $data['Settings_Adjusted'] + $data['HealthENBuddy_Adjusted'] + $data['ENBuddy_Adjusted'];
        $data['Total_304'] = ($data['HealthENBuddy_304'] ?? 0) + ($data['ENBuddy_304'] ?? 0) + ($data['Google-ENExpress_304'] ?? 0);
        return $data;
    }

    private function renameFields($data) {
        $rekeyedData = array();
        foreach ($data as $k => $d) {
            if (strpos($k, "_304") !== false) {
                $rekeyedData[$this->fieldNameMap[substr($k, 0, strlen($k)-4)] ?? $k] = $d;
            } else {
                $rekeyedData[$this->fieldNameMap[$k] ?? $k] = $d;
            }
        }
        return $rekeyedData;
    }

    private function sumSettingsFields($data) {
        $sum = 0;
        foreach (array_values($this->fieldNameMap) as $key) {
            $sum += $data[$key] ?? 0;
        }
        return $sum;
    }
}