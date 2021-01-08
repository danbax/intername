<?php
session_start();

define('Access', true);
define('DEV_MODE', true);
define('SECURE_AJAX', true);
define('DBHOST', 'localhost');
define('CLASSES_DIR', str_replace("includes", "classes", __DIR__));
define('INCLUDES_DIR', __DIR__);
define('RESULT_ERROR', 'ERROR');
define('RESULT_SUCCESS', 'OK');
define("VERSION","2.1");

date_default_timezone_set ( 'Asia/Jerusalem' );

if (DEV_MODE === true) {

    ini_set ('display_errors', 'on');
    ini_set ('log_errors', 'on');
    ini_set ('display_startup_errors', 'off');
    ini_set ('error_reporting', E_ALL);

    //db details
    define('DBUSER', 'dani');
    define('DBPASS', 'db160595dbB');
    define('DBNAME', 'intername');

} else {

    ini_set ('display_errors', 'off');
    ini_set ('log_errors', 'on');
    ini_set ('display_startup_errors', 'off');
    ini_set ('error_reporting', E_ALL);

    //db details
    define('DBUSER', 'SBTenders_user');
    define('DBPASS', 'BiwEsLo1iv');
    define('DBNAME', 'SBTenders_db');

    if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off") {

        $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $redirect);
        exit;
    }
}