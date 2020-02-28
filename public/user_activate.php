<?php

// configuration
require("../includes/config.php");

if (!empty($_SESSION["user_id"])) {
    redirect("/");
}

$message = 'Lo sentimos no pudimos activar su cuenta!';

if (
    $_SERVER["REQUEST_METHOD"] == "GET"
    && isset($_GET['email'], $_GET['key'])
    && checkFormat($regexEmail, $_GET['email'], true)
    && checkFormat($regexKey, $_GET['key'])
) {
    $useremail = test_input($_GET['email']);
    $activation_key = test_input($_GET['key']);

    if (activateUserAccount($useremail, $activation_key)) {

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
