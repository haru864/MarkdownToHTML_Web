<?php

use Controllers\ConvertController;
use Controllers\StaticFileController;
use Http\HttpRequest;
use Services\ConvertService;
use Services\StaticFileService;
use Settings\Settings;

$httpRequest = new HttpRequest();
$convertService = new ConvertService();
$convertController = new ConvertController($convertService, $httpRequest);
$staticFileService = new StaticFileService();
$staticFileController = new StaticFileController($staticFileService, $httpRequest);

$PATTERN_CONVERT_URL_DIR = Settings::env("PATTERN_CONVERT_URL_DIR");
$PATTERN_STATIC_FILE_URL_DIR = Settings::env("PATTERN_STATIC_FILE_URL_DIR");

return [
    $PATTERN_CONVERT_URL_DIR => $convertController,
    $PATTERN_STATIC_FILE_URL_DIR => $staticFileController
];
