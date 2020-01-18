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
    '/activate.php',
    '/reset_password.php'
];

// PHP_SELF: /reset_password.php
if ( !in_array($_SERVER["PHP_SELF"], $pages) ) {
    
    if (empty($_SESSION["user_id"])) {
        redirect("login.php");
    }
}

if (!empty($_SESSION["user_id"])) {
    redirect("/");
}

$title = 'Restablecer contraseña';

// user_id=3
// key=cf65f7f964865432e15deeac39d44668
$validKey = '/^[a-z0-9]{32}$/';

$errors = [];

if( $_SERVER['REQUEST_METHOD'] == 'POST'
    && isPositiveInt( (int) $_POST['user_id'])
    && checkFormat($validKey, $_POST['key'])
) {

    $user['user_id'] = test_input( $_POST['user_id']);
    $reset_key = test_input( $_POST['key']);
    
    $validPassword = false;
    if (empty($_POST['new_password'])) {
        $errors['new_password'] = 'Crea una contraseña.';
    } else {
        $user['new_password'] = test_input( $_POST['new_password'] );

        $minPasswordLength = 8;
        $maxPasswordLength = 64;

        if(!checkLength($user['new_password'], $minPasswordLength, $maxPasswordLength) ){
            $errors['new_password'] = 'Su contraseña debe tener entre ' . $minPasswordLength . ' y ' . $maxPasswordLength . ' caracteres.';
        } elseif( !validatePasswordStrength($user['new_password']) ){
            $errors['new_password'] = 'La contraseña debe incluir al menos una letra mayúscula, un número y un carácter especial.';
        } else {
            $validPassword = true;
        }
    }

    if (empty($_POST['confirm_new_password'])) {
        $errors['confirm_new_password'] = 'Confirme su nueva contraseña.';
    } else {
        $user['confirm_new_password'] = test_input( $_POST['confirm_new_password'] );

        if ($validPassword) {
            // Comparación segura a nivel binario sensible a mayúsculas y minúsculas.
            if (strcmp($user['new_password'], $user['confirm_new_password']) !== 0) {
                $errors['confirm_new_password'] = 'Las contraseñas no coinciden.';
            }
        }
    }

    if (count($errors) == 0) {

        $user['new_password'] = encryptPassword($user['new_password']);
        
        if ( resetUserPassword($user, $reset_key) ) {
            Flash::addFlash('Su contraseña fue restablecida, ahora puede iniciar sesión.');
            redirect('/');
        }
    }

    // render header
    require("../views/inc/header.html");
            
    // render template
    require("../views/user/reset_password.html");
        
    // render footer
    require("../views/inc/footer.html");
    exit;
}

$message = 'Algo salió mal. Vuelva a comprobar el enlace o póngase en contacto con el administrador del sistema.';

if ( $_SERVER["REQUEST_METHOD"] == "GET"
    && isset($_GET['user_id'], $_GET['key'])
    && isPositiveInt( (int) $_GET['user_id'])
    && checkFormat($validKey, $_GET['key'])
) {
    $user['user_id'] = test_input( $_GET['user_id']);
	$reset_key = test_input( $_GET['key']);
    
    $res = getUserIdFromForgotPassword($user['user_id'], $reset_key);
    
	if ( count($res) == 1 ) {

        // render header
        require("../views/inc/header.html");
            
        // render template
        require("../views/user/reset_password.html");
            
        // render footer
        require("../views/inc/footer.html");
        exit;
	} else {
        $message = 'Credenciales no válidas o su contraseña ya fue restablecida!';
    }
}

// render header
require("../views/inc/header.html");

// render template
echo '<div class="text-center" style="width: 100%;max-width: 600px;margin: auto;margin-top: 200px;">
<h3 style="font-weight: 400;">' . $message . '</h3></div>';

// render footer
require("../views/inc/footer.html");

/**
 * Funciones de persistencia
 */
function getUserIdFromForgotPassword($user_id, $reset_key)
{
    $time = time() - 1200;

    // compruebe que la combinación de user_id & key exista y tenga menos de 20 m de antigüedad
    $q = 'SELECT user_id FROM forgot_password
          WHERE
            reset_key = ?
          AND
            user_id = ?
          AND
            time > ?
          AND
            status = "pending";';
              
    return Db::query($q, $reset_key, $user_id, $time);
}

function resetUserPassword($user, $reset_key)
{
    $last_modified_on = date("Y-m-d H:i:s");

    $q = 'UPDATE USER 
          SET
            password = ?,
            last_modified_on = ?
          WHERE user_id = ?;';
    
    $result = Db::query($q, $user['new_password'], $last_modified_on, $user['user_id']);

    if(!$result) return false;

    $q = 'UPDATE forgot_password 
          SET
            STATUS = "used",
            last_modified_on = ? 
          WHERE reset_key = ? 
          AND user_id = ? ;';

    return Db::query($q, $last_modified_on, $reset_key, $user['user_id']);
}