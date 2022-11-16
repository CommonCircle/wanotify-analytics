<?php 

class DatedData {
    private $data;

    public function __construct($data=array()) {
        $this->setDataArray($data);
    }

    public function addData($date, $data) {
        if ($date instanceof DateTime) {
            $date = $date->format('Y-m-d H:i:s');
        }
        $date = $this->validateDate($date);
        $this->data[$date] = $data;
        return;
    }

    public function setDataArray($dataArray) {
        $this->data = array();
        foreach ($dataArray as $date => $data) {
            $this->addData($date, $data);
        }
        return;
    }

    public function getData() {
        return $this->data;
    }

    public function getEncodedData() {
        return array_map('json_encode', $this->data);
    }

    public function merge(DatedData $dataToMerge) {
        foreach ($dataToMerge->getData() as $date => $newData) {
            $updatedData = array_merge($this->data[$date] ?? [], $newData);
            $this->addData($date, $updatedData);
        }
        return $this;
    }

    private function validateDate($date) {
        $dt = new DateTime($date);
        if (checkdate($dt->format('m'), $dt->format('d'), $dt->format('Y'))) {
            $date = $dt->format('Y-m-d H:i:s');
        } else {
            throw new Exception("Invalid date $date");
        }
        return $date;
    }
}
?>