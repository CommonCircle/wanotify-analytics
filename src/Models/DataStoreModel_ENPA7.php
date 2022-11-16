<?php
require_once __DIR__ . '/DataStoreModel.php';

class DataStoreModel_ENPA7 extends DataStoreModel {
    protected $baseTableName = 'enpa_7d_data';
    protected $importInterval = 'daily';
    protected $intervalSuffixes = array(
        'daily' => 'P1D',
        'weekly' => 'P7D',
    );

    /**
     * startDate: inclusive start date of configuration
     * endDate: exclusive end date of configuration
     * map: array mapping each classification ranking to a human readable label.
     *      Null mappings are dropped for that time period.
     */
    protected $classificationLabelMaps = array(
        array(
            'startDate' => '2020-11-30 00:00:00',
            'endDate' => '2021-07-27 00:00:00',
            'map' => array(
                '1' => 'Standard',
                '2' => null,
                '3' => null,
                '4' => null,
            )
        ),
        array(
            'startDate' => '2021-07-27 00:00:00',
            'endDate' => '2021-12-16 00:00:00',
            'map' => array(
                '1' => 'Extended',
                '2' => 'Standard',
                '3' => null,
                '4' => null,
            )
        ),
        array(
            'startDate' => '2021-12-16 00:00:00',
            'endDate' => '2021-12-24 00:00:00',
            'map' => array(
                '1' => 'Self-report',
                '2' => 'Extended',
                '3' => 'Standard',
                '4' => null,
            )
        ),
        array(
            'startDate' => '2021-12-24 00:00:00',
            'endDate' => '2022-05-03 00:00:00',
            'map' => array(
                '1' => 'Self-report',
                '2' => 'Extended',
                '3' => 'Standard',
                '4' => 'Advisory',
            )
        ),
        array(
            'startDate' => '2022-05-03 00:00:00',
            'endDate' => false,
            'map' => array(
                '1' => '8-24h',
                '2' => '2-8h',
                '3' => '30-120m',
                '4' => '7-30m',
            )
        ),
    );

    public function __construct($pdo) {
        parent::__construct($pdo);
    }

    // Synthetic fields added here
    protected function prepareData(DatedData $datedData) : DatedData {
        $dd = $datedData->getData();
        $change = $this->classificationLabelMaps[0];
        foreach ($dd as $date => $data) {
            if ($date < $change['startDate'] || ($change['endDate'] && $date >= $change['endDate'])) {
                // Find the right change period
                foreach ($this->classificationLabelMaps as $c) {
                    if ($date >= $c['startDate'] && (!$c['endDate'] || $date < $c['endDate'])) {
                        $change = $c;
                        break;
                    }
                }
            }
            // Relabel changes for the period
            foreach ($data as $key => $value) {
                // Look for a field name with a one that isn't part of a range (e.g. 1-2) or multi-digit number (e.g. 11)
                if (preg_match("/(?<!\d-|\d)1(?!-\d|\d)/", $key, $matches) && array_key_exists('1', $change['map'])) {
                    $label = $change['map']['1'];
                    if ($label !== null) {
                            $data[preg_replace("/(?<!\d-|\d)1(?!-\d|\d)/", " " . $label, $key, 1)] = $data[$key];
                    }
                    unset($data[$key]);
                } else if (preg_match("/(?<!\d-|\d)2(?!-\d|\d)/", $key, $matches) && array_key_exists('2', $change['map'])) {
                    $label = $change['map']['2'];
                    if ($label !== null) {
                            $data[preg_replace("/(?<!\d-|\d)2(?!-\d|\d)/", " " . $label, $key, 1)] = $data[$key];
                    }
                    unset($data[$key]);
                } else if (preg_match("/(?<!\d-|\d)3(?!-\d|\d)/", $key, $matches) && array_key_exists('3', $change['map'])) {
                    $label = $change['map']['3'];
                    if ($label !== null) {
                            $data[preg_replace("/(?<!\d-|\d)3(?!-\d|\d)/", " " . $label, $key, 1)] = $data[$key];
                    }
                    unset($data[$key]);
                } else if (preg_match("/(?<!\d-|\d)4(?!-\d|\d)/", $key, $matches) && array_key_exists('4', $change['map'])) {
                    $label = $change['map']['4'];
                    if ($label !== null) {
                            $data[preg_replace("/(?<!\d-|\d)4(?!-\d|\d)/", " " . $label, $key, 1)] = $data[$key];
                    }
                    unset($data[$key]);
                }
            }

            $datedData->addData($date, $data);
        }
        return $datedData;
    }
}
