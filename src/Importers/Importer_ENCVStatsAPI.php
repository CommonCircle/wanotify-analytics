<?php
require_once __DIR__ . "/Importer_JsonFile.php";

abstract class Importer_ENCVStatsAPI extends Importer_JsonFile {

    protected $sourceDateTimeFormat = 'Y-m-d\TH:i:s\Z';

}
