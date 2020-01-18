<?php

// display errors, warnings, and notices
ini_set("display_errors", true);
error_reporting(E_ALL);

require("../includes/helpers.php");

// enable sessions
session_start();

$pages = [
    '/login.php',
    '/logout.php',
    '/register.php'
];

// PHP_SELF: /logout.php
if ( !in_array($_SERVER["PHP_SELF"], $pages) ) {
    
    if (empty($_SESSION["user_id"])) {
        redirect("login.php");
    }
}

logout();

// redirect user
redirect("/");

function logout()
{
    // Unset all of the session variables.
    $_SESSION = [];

    // destroy the session
    session_destroy();
}