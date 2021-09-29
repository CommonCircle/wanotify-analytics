<?php
require_once UTIL_DIR . '/DatedData.php';

abstract class DataStoreModel {
    protected $pdo;

    protected $baseTableName;

    protected $importTableName;

    protected $importInterval;

    protected $intervalSuffixes = array();

    public $intervalString;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
        $this->importTableName = $this->baseTableName . "_" . $this->importInterval;
        $this->intervalString = $this->intervalSuffixes[$this->importInterval];
    }

    abstract protected function prepareData(DatedData $data) : DatedData;

    public function save(DatedData $datedData) {
        $datedData = $this->prepareData($datedData);
        $this->upsert($datedData);
    }

    protected function upsert(DatedData $dataByDate) {
        $newData = $dataByDate->getEncodedData();

        $dates = array_keys($newData);
        $count = count($newData);
        $placeholders = implode(',', array_fill(0,$count,'?'));
        
        try {
            // get current data that may be overwritten
            $sql = "SELECT date, data FROM $this->importTableName WHERE date IN ($placeholders)";
            $stmt = $this->pdo->prepare($sql);
            $params = $dates;
            $stmt->execute($params);
            $oldDataByDate = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (PDOException $e) {
            // Don't update anything if changes can't be recorded
            throw $e;
        }
    
        $updateTime = date('Y-m-d H:i:s');
    
        $upsertSql = "INSERT INTO $this->importTableName (date, data) VALUES (:date, :data) ON DUPLICATE KEY UPDATE data = VALUES(data)";
        $upsertStmt = $this->pdo->prepare($upsertSql);
    
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
                        'table_name' => $this->importTableName,
                        'date' => $date,
                        'old_data' => $oldData,
                        'new_data' => $data,
                        'modified' => $updateTime,
                    );
                    $logStmt->execute($logParams);
                }
            } catch (PDOException $e) {
                error_log("Unable to upsert new $date data to $this->importTableName.", 3, '/home/dlorigan/analytics/logs/error_log.txt');
                $this->pdo->rollback();
                throw $e;
            }
        }
        // successfully upserted and logged changes
        $this->pdo->commit();
    }

    /**
     * input
     * $interval: String - either 'hourly', 'daily', or 'weekly'
     * $startDateTime: DateTime
     * $endDateTime: DateTime
     * 
     * output
     * array(array(date => DateTime, data => array(keyed data)))
     */
    public function selectDataIntervalByDate($interval, $startDateTime=null, $endDateTime=null) {
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
                    $whereClause .= "date <= :endDateTime";
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

    private function getTableNameForInterval($interval) {
        if (array_key_exists($interval, $this->intervalSuffixes)) {
            return $this->baseTableName . "_" . $interval;
        }
        return null;
    }

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