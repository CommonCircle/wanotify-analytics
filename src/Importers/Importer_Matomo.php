<?php
require_once __DIR__ . "/Importer_JsonFile.php";

abstract class Importer_Matomo extends Importer_JsonFile {

    protected $sourceDateTimeFormat = 'Y-m-d|';

}
