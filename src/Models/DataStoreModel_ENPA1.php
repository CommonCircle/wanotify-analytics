<?php
require_once __DIR__ . '/DataStoreModel.php';

class DataStoreModel_ENPA1 extends DataStoreModel {
    protected $baseTableName = 'enpa_1d_data';
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

    // Looks for notification types (e.g. 1-4) that aren't part of a range (e.g. 1-2), multi-digit number (e.g. 11), and aren't followed by letters.
    private function reForNotificationType($typeLabel) {
        return "/(?<!\d-|\d)$typeLabel(?!-\d|\d|[A-Za-z])/";
    }

    private function hasNotificationType($key, $typeLabel) {
        return preg_match($this->reForNotificationType($typeLabel), $key, $matches);
    }

    // Synthetic fields added here
    protected function prepareData(DatedData $datedData) : DatedData {
        $dd = $datedData->getData();
        $change = $this->classificationLabelMaps[0];
        foreach ($dd as $date => $data) {
            // Check if current entry is outside of current change period
            if ($date < $change['startDate'] || ($change['endDate'] && $date >= $change['endDate'])) {
                // Switch to the right change period
                foreach ($this->classificationLabelMaps as $c) {
                    if ($date >= $c['startDate'] && (!$c['endDate'] || $date < $c['endDate'])) {
                        $change = $c;
                        break;
                    }
                }
            }
            // Relabel notification types for the change period
            foreach ($data as $key => $value) {
                foreach ($change['map'] as $notificationType => $label) {
                    if ($this->hasNotificationType($key, $notificationType)) {
                        if ($label !== null) {
                                $data[preg_replace($this->reForNotificationType($notificationType), " " . $label, $key, 1)] = $data[$key];
                        }
                        unset($data[$key]);
                    }    
                }
            }

            $datedData->addData($date, $data);
        }
        return $datedData;
    }
}
