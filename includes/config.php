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

// enable sessions
session_start();

$pages = [
    '/login.php',
    '/logout.php',
    '/register.php',
    '/forgot_password.php',
    '/reset_password.php',
    '/activate.php'
];

// PHP_SELF: /página.php no se encuentra en el array $pages entonces la condición se cumple
if ( !in_array($_SERVER["PHP_SELF"], $pages) ) {
    
    if (empty($_SESSION["user_id"])) {
        redirect("login.php");
    }
}