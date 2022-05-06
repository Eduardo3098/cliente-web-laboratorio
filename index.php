<?php
    error_reporting(E_ALL);

    ini_set('ignore_repeated_errors', true);

    ini_set('display_errors', false);

    ini_set('log_errors', true);

    ini_set('error_log', './php-error.log');

    error_log('Inicio de aplicacion web');

    require_once 'libs/Database.php';
    require_once 'classes/ErrorMessages.php';
    require_once 'classes/SuccessMessages.php';
    require_once 'libs/Controller.php';
    require_once 'libs/Model.php';
    require_once 'libs/View.php';
    require_once 'libs/App.php';

    require_once 'config/config.php';

    $app = new App();