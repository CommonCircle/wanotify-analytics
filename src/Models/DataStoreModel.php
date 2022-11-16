<?php
require_once UTIL_DIR . '/DatedData.php';

class DataStoreModel {
    protected $pdo;

    protected $baseTableName;

    protected $importTableName;

    protected $importInterval;

    protected $intervalSuffixes = array();
    
    protected $rateFields = array();

    protected $cumulativeFields = array();

    public $importIntervalString;

    public const INCLUSIVE_UPPER = true;
    public const UNINCLUSIVE_UPPER = false;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
        $this->importTableName = $this->getTableNameForInterval($this->importInterval);
        $this->importIntervalString = $this->intervalSuffixes[$this->importInterval];
    }

    public function save(DatedData $datedData) {
        $datedData = $this->prepareData($datedData);
        $this->upsert($datedData);
    }

    /**
     * input
     * $interval: String - either 'hourly', 'daily', or 'weekly'
     * $startDateTime: DateTime or string
     * $endDateTime: DateTime or string
     * 
     * output
     * array(array(date => DateTime, data => array(keyed data)))
     */
    public function selectDataByIntervalAndRange($interval, $startDateTime=null, $endDateTime=null, $includeUpperBound=self::INCLUSIVE_UPPER) {
        if ($startDateTime instanceof DateTime) {
            $startDateTime = $startDateTime->format('Y:m:d H:i:s');
        }
        if ($endDateTime instanceof DateTime) {
            $endDateTime = $endDateTime->format('Y:m:d H:i:s');
        }
        $upperBoundOperator = $includeUpperBound == self::INCLUSIVE_UPPER ? "<=" : "<";

        $tableName = $this->getTableNameForInterval($interval);
        $result = null;
        if ($tableName) {
            $dateCol = "date";
            $dataCol = "data";
            $SQL = "SELECT $dateCol, $dataCol FROM $tableName";
            $params = array();
            if ($startDateTime !== null || $endDateTime !== null) {
                $whereClause = " WHERE ";
                if ($startDateTime) {
                    $whereClause .= "date >= :startDateTime";
                    $params['startDateTime'] = $startDateTime;
                }
                if ($endDateTime) {
                    if ($startDateTime) {
                        $whereClause .= " AND ";
                    }
                    $whereClause .= "date $upperBoundOperator :endDateTime";
                    $params['endDateTime'] = $endDateTime;
                }
                $SQL .= $whereClause;
                $SQL .= " ORDER BY date";
            }
            $stmt = $this->pdo->prepare($SQL);
            $stmt->execute($params);
            $result = $stmt->fetchAll(\PDO::FETCH_FUNC, function($a,$b) use ($dateCol, $dataCol) { return [$dateCol => $a, $dataCol => json_decode($b,true)]; });
        }
        return $result;
    }

    /**
     * Utility to re-process existing table records
     * May require custom tooling for specific needs e.g. renaming fields
     */
    public function refreshData() {
        // Get contents from data store
        $contents = $this->selectDataByIntervalAndRange($this->importInterval);
        $datedData = new DatedData();
        
        foreach ($contents as $entry) {
            $date = $entry['date'];
            // Convert numeric strings to int/float
            $data = array_map(
                function ($val) {
                    if (is_numeric($val)) {
                        return $val + 0;
                    }
                    return $val;
                },
                $entry['data']
            );
            $datedData->addData($date, $data);
        }
        // Prepare and save data
        $this->save($datedData);

        // Regenerate aggregate interval tables
        if ($this->importInterval == 'hourly') {
            $this->generateDataInterval('daily');
        } // TODO: replace with the following when weekly interval is supported
        //foreach($this->intervalSuffixes as $suffix => $interval) {
            //if ($suffix != $this->importInterval) {
            //    $this->generateDataInterval($suffix);
            //}
        //}
    }

    /**
     * Basic insert/overwrite functionality
     */
    protected function standardUpsert(DatedData $dataByDate, $interval=null) {
        if ($interval === null) {
            $interval = $this->importInterval;
        }
        $tableName = $this->getTableNameForInterval($interval);

        $newData = $dataByDate->getEncodedData();

        if (sizeof($newData) > 0) {
            $dates = array_keys($newData);
            $count = count($newData);
            $placeholders = implode(',', array_fill(0,$count,'?'));
            
            try {
                // get current data that may be overwritten
                $sql = "SELECT date, data FROM $tableName WHERE date IN ($placeholders)";

                $stmt = $this->pdo->prepare($sql);
                $params = $dates;
                $stmt->execute($params);
                $oldDataByDate = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            } catch (PDOException $e) {
                // Don't update anything if changes can't be recorded
                throw $e;
            }
        
            $updateTime = date('Y-m-d H:i:s');
        
            // Simply overwrite current row data with new structure
            $upsertSql = "INSERT INTO $tableName (date, data) VALUES (:date, :data) ON DUPLICATE KEY UPDATE data = VALUES(data)";
            $upsertStmt = $this->pdo->prepare($upsertSql);
        
            // Log current and new row if changes occur
            $logSql = "INSERT INTO update_logs (table_name, date, old_data, new_data, modified) VALUES (:table_name, :date, :old_data, :new_data, :modified)";
            $logStmt = $this->pdo->prepare($logSql);
        
            $this->pdo->beginTransaction();
            foreach ($newData as $date => $data) {
                try {
                    $upsertParams = array(
                        'date' => $date,
                        'data' => $data,
                    );
                    $upsertStmt->execute($upsertParams);
                    $updated = ($upsertStmt->rowCount() === 2); // check that upsert updated in mysql
                    if ($updated) {
                        $oldData = $oldDataByDate[$date];
                        $logParams = array(
                            'table_name' => $tableName,
                            'date' => $date,
                            'old_data' => $oldData,
                            'new_data' => $data,
                            'modified' => $updateTime,
                        );
                        $logStmt->execute($logParams);
                    }
                } catch (PDOException $e) {
                    error_log("Unable to upsert new $date data to $tableName.", 3, '/home/dlorigan/analytics/logs/error_log.txt');
                    $this->pdo->rollback();
                    throw $e;
                }
            }
            // successfully upserted and logged changes
            $this->pdo->commit();
        }
    }

    protected function mergeUpsert(DatedData $dataByDate, $interval=null) {
        if ($interval === null) {
            $interval = $this->importInterval;
        }
        $result = $this->selectDataByIntervalAndRange($interval);
        $resultByDate = array_combine(array_column($result, 'date'), array_column($result, 'data'));
        $currentData = new DatedData($resultByDate);
        $mergedData = $currentData->merge($dataByDate);
        $this->standardUpsert($mergedData, $interval);
    }

    protected function upsert(DatedData $dataByDate, $interval=null) {
        return $this->standardUpsert($dataByDate, $interval);
    }

    private function getLastFullPeriod($interval) {
        $tableName = $this->getTableNameForInterval($interval);
        switch ($interval) {
            case 'hourly':
                $fullPeriodCount = 24;
                break;
            case 'daily':
                $fullPeriodCount = 7;
                break;
            default:
                return false;
        }
        $tableName = $this->getTableNameForInterval($interval);
        $result = null;
        if ($tableName) {
            $dateCol = "date";
            $formattedDateCol = "period_start_date";
            $dateCountCol = "date_count";

            $SUBQUERY = "SELECT DATE_FORMAT($dateCol, '%Y-%m-%d') AS $formattedDateCol, COUNT(*) AS $dateCountCol
                FROM $tableName
                GROUP BY $formattedDateCol
                ORDER BY $dateCol DESC";

            $SQL = "SELECT $formattedDateCol FROM ($SUBQUERY) AS sq
                WHERE sq.$dateCountCol = 24
                LIMIT 1";
            
            $stmt = $this->pdo->prepare($SQL);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_COLUMN, $formattedDateCol);
        }
        if (sizeof($result) == 1) {
            return $result[0];
        } else {
            return $result;
        }
    }

    public function generateDataInterval($interval, $stopDate='2000-01-01') {
        if (array_key_exists($interval, $this->intervalSuffixes)) {
            if (!($stopDate instanceof DateTime)) {
                $stopDate = new DateTime($stopDate);
                $stopDate->settime(0,0,0);
            }

            switch ($interval) {
                case 'daily':
                    $baseInterval = 'hourly';
                    $lastComplete = 'today midnight';
                    break;
                case 'weekly':
                    $baseInterval = 'daily';
                    $lastComplete = 'last monday'; # TODO change to two mondays ago
                    break;
                default:
                    return false;
            }

            $lastComplete = $this->getLastFullPeriod($baseInterval);
            $lastCompleteDT = DateTime::createFromFormat('Y-m-d|', $lastComplete);
            
            $dateInterval = new DateInterval($this->intervalSuffixes[$interval]);
            $baseDateInterval = new DateInterval($this->intervalSuffixes[$baseInterval]);
            $firstIncompleteDT = $lastCompleteDT->add($dateInterval);
            $endDT = (clone $firstIncompleteDT);
            $startDT = (clone $firstIncompleteDT)->sub($dateInterval);

            
            $baseData = $this->selectDataByIntervalAndRange($baseInterval, $startDT, $endDT, self::UNINCLUSIVE_UPPER);
            $newIntervalData = new DatedData();
            // TODO: Weeks -- check partial week at early end of data
            while (sizeof($baseData) > 0 && $endDT > $stopDate) {
                $newEntry = array();
                // sum each field over the interval
                foreach ($baseData as $day) {
                    $data = $day['data'];
                    // Sum all existing fields, rate fields will be recalculated
                    foreach ($data as $key => $value) {
                        $newEntry[$key] = ($newEntry[$key] ?? 0) + $value;
                    }
                }
                // Recalculate any rate fields that were summed
                $newEntry = $this->calculateRateFields($newEntry);
                $newIntervalData->addData($startDT, $newEntry);
                
                $startDT->sub($dateInterval);
                $endDT->sub($dateInterval);
                $baseData = $this->selectDataByIntervalAndRange($baseInterval, $startDT, $endDT, self::UNINCLUSIVE_UPPER);
            }
            $this->upsert($newIntervalData, $interval);

        } else {
            return false;
        }
    }

    public function generateCumulativeFields($interval) {
        if (array_key_exists($interval, $this->intervalSuffixes)) {
            $dateInterval = new DateInterval($this->intervalSuffixes[$interval]);
            
            $baseData = $this->selectDataByIntervalAndRange($interval, $startDateTime="2020-11-30 00:00:00");
            $newIntervalData = new DatedData();
            $totals = array_fill_keys(array_keys($this->cumulativeFields), 0);
            foreach ($baseData as $day) {
                $newEntry = $day['data'];
                $date = $day['date'];
                foreach ($this->cumulativeFields as $cumulativeField => $individualField) {
                    $totals[$cumulativeField] += ($newEntry[$individualField] ?? 0);
                    $newEntry[$cumulativeField] = ($totals[$cumulativeField] ?? 0);
                }
                $newIntervalData->addData($date, $newEntry);
            }
            $this->upsert($newIntervalData, $interval);
        } else {
            return false;
        }
    }

    private function getTableNameForInterval($interval) {
        if (array_key_exists($interval, $this->intervalSuffixes)) {
            return $this->baseTableName . "_" . $interval;
        }
        return null;
    }

    // Synthetic fields added here
    protected function prepareData(DatedData $datedData) : DatedData {
        $dd = $datedData->getData();
        foreach($dd as $date => $data) {
            $data = $this->generateCalculatedFields($data);
            $datedData->addData($date, $data);
        }
        return $datedData;
    }

    protected function generateCalculatedFields($data) {
        $data = $this->calculateSumFields($data);
        $data = $this->calculateRateFields($data);
        return $data;
    }

    // Only called at import
    protected function calculateSumFields($data) { return $data; }
    // Called at import and generateDataInterval() aggregation
    protected function calculateRateFields($data) { return $data; }
    // 
    protected function calculateCumulativeFields($data) { return $data; }

    // Returns the sum of the specified fields
    protected function sum($d, ...$fields) {
        return array_sum(array_filter($d, function($k) {
            return in_array($k, $fields);
        }, ARRAY_FILTER_USE_KEY));
    }

    // TODO
    protected function sumAll($fieldName, $data, ...$fields) {
        return array_map(function($d){
            $d[$fieldName] = $this->sum($d, ...$fields);
            return $d;
        },);
    }

}