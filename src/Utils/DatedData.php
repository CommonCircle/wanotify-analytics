<?php 

class DatedData {
    private $data;

    public function __construct() {
        $this->data = array();
    }

    public function setData($date, $data) {
        $success = true;
        
        $date = $this->validateDate($date);
        
        if ($date) {
            $this->data[$date] = $data;
        } else {
            $success = false;
        }
        return $success;
    }

    public function getData() {
        return $this->data;
    }

    public function getEncodedData() {
        return array_map('json_encode', $this->data);
    }

    private function validateDate($date) {
        $dt = new DateTime($date);
        if (checkdate($dt->format('m'), $dt->format('d'), $dt->format('Y'))) {
            $date = $dt->format('Y-m-d H:i:s');
        } else {
            return false;
        }
        return $date;
    }
}
?>