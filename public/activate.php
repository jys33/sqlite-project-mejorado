<?php

// display errors, warnings, and notices
ini_set("display_errors", true);
error_reporting(E_ALL);

require("../includes/helpers.php");
require("../src/app/Db.php");
require("../src/app/Flash.php");

// enable sessions
session_start();

$pages = [
    '/login.php',
    '/logout.php',
    '/register.php',
    '/activate.php'
];

// PHP_SELF: /activate.php
if ( !in_array($_SERVER["PHP_SELF"], $pages) ) {
    
    if (empty($_SESSION["user_id"])) {
        redirect("login.php");
    }
}

if (!empty($_SESSION["user_id"])) {
    redirect("/");
}

$validEmail = '/^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$/';
$validKey = '/^[a-z0-9]{32}$/';

$message = 'Lo sentimos no pudimos activar su cuenta!';

if ( $_SERVER["REQUEST_METHOD"] == "GET"
    && isset($_GET['email'], $_GET['key'])
    && checkFormat($validEmail, $_GET['email'], true)
    && checkFormat($validKey, $_GET['key'])
) {
    $useremail = test_input( $_GET['email'] );
    $activation_key = test_input( $_GET['key'] );

    if ( activateUserAccount($useremail, $activation_key) ) {

        Flash::addFlash('Su cuenta ha sido activada, ahora puede iniciar sesión!');
        redirect('/');

    } else {
        $message = 'Credenciales no válidas o ya ha activado su cuenta!';
    }
}

require("../views/error/error.html");
exit;

/**
 * Funciones de persistencia
 */
function activateUserAccount($useremail, $activation_key)
{
	$q = 'UPDATE
	        user
		  SET activation = "activated"
		  WHERE (user_email = ?
		  AND activation = ?);';

    return Db::query($q, $useremail, $activation_key);
}