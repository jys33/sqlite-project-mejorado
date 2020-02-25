<?php

// display errors, warnings, and notices
ini_set("display_errors", true);
error_reporting(E_ALL);

date_default_timezone_set('AMERICA/ARGENTINA/BUENOS_AIRES');
mb_internal_encoding('UTF-8');
setlocale(LC_TIME, 'es_RA.UTF-8');

// requirements
require("constants.php");
require("../includes/helpers.php");
require("../src/app/Db.php");
require("../src/app/Flash.php");
// https://wiki.php.net/rfc/automatic_csrf_protection#dokuwiki__top
// https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html
// enable sessions
session_name('ID');
session_start();

$pages = [
    '/user_activate.php',
    '/user_login.php',
    '/user_logout.php',
    '/user_register.php',
    '/user_forgot_password.php',
    '/user_reset_password.php'
];

// PHP_SELF: /página.php no se encuentra en el array $pages entonces la condición se cumple
if ( !in_array($_SERVER["PHP_SELF"], $pages) ) {
    
    if (empty($_SESSION["user_id"])) {
        redirect("user_login.php");
    }
}