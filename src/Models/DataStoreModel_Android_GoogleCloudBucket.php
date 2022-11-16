<?php
require_once __DIR__ . '/DataStoreModel_Android.php';

class DataStoreModel_Android_GoogleCloudBucket extends DataStoreModel_Android {
    protected $importInterval = 'daily';
    protected $intervalSuffixes = array(
        'daily' => 'P1D',
        'weekly' => 'P7D',
    );
}