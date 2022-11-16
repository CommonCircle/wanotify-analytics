<?php

require_once __DIR__ . '/Importer_WebLogs.php';
require_once MODEL_DIR . '/DataStoreModel_LandingPage.php';

class Importer_LandingPage_Raw extends Importer_WebLogs {

    public function __construct(\PDO $pdo, $location, $authType=null, $authVal=null) {
        $this->resourceNames = array(
            "/ExposureNotification/exposure/",
            "/ExposureNotification/exposure/en",
            "/ExposureNotification/exposure/es",
            "/ExposureNotification/exposure/zh-cn",
            "/ExposureNotification/exposure/ja",
            "/ExposureNotification/exposure/zh-hk",
            "/ExposureNotification/exposure/ko",
            "/ExposureNotification/exposure/vi",
            "/ExposureNotification/exposure/ru",
            "/ExposureNotification/exposure/fr",
            "/ExposureNotification/exposure/ar",
            "/ExposureNotification/exposure/am",
            "/ExposureNotification/exposure/my",
            "/ExposureNotification/exposure/fa",
            "/ExposureNotification/exposure/de",
            "/ExposureNotification/exposure/hi",
            "/ExposureNotification/exposure/km",
            "/ExposureNotification/exposure/pt",
            "/ExposureNotification/exposure/pa",
            "/ExposureNotification/exposure/ro",
            "/ExposureNotification/exposure/so",
            "/ExposureNotification/exposure/sw",
            "/ExposureNotification/exposure/tl",
            "/ExposureNotification/exposure/ta",
            "/ExposureNotification/exposure/th",
            "/ExposureNotification/exposure/tr",
            "/ExposureNotification/exposure/uk",
            "/ExposureNotification/exposure/ur",
        );
        $model = new DataStoreModel_LandingPage($pdo);
        parent::__construct($model, $location, $authType, $authVal);
    }
}
