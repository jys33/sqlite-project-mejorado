<?php

// configuration
require("../includes/config.php");

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