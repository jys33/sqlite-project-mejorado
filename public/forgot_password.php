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
    '/forgot_password.php'
];

// PHP_SELF: /forgot_password.php
if ( !in_array($_SERVER["PHP_SELF"], $pages) ) {
    
    if (empty($_SESSION["user_id"])) {
        redirect("login.php");
    }
}

if (!empty($_SESSION["user_id"])) {
    redirect("/");
}


$title = "Olvidó su contraseña";
$errors = [];
$user['user_email'] = '';

if( $_SERVER['REQUEST_METHOD'] == 'POST') {

    $validEmail = '/^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$/';

    if (empty($_POST['user_email'])) {
        $errors['user_email'] = 'Ingrese su correo electrónico.';
    } else {
        $user['user_email'] = test_input( $_POST['user_email'] );

        if ( !checkFormat($validEmail, $user['user_email'], true) ) {
            $errors['user_email'] = '\''. $user['user_email'] . '\' no es una dirección de correo electrónico válida.';
        }
    }

    if (count($errors) == 0) {
        // Consulta a base de datos
        $rows = getUserByEmail($user['user_email']);
            
        if (count($rows) != 1) {
            $errors['user_email'] = 'Lo sentimos, no encontramos ese correo electrónico.';
        } else {

            $row = $rows[0];

            $user['user_id'] = $row['user_id'];
            /**
            * Create a unique activation code 32 caracteres
            * Ejemplo KEY: cc58481ee70ce0027209abf27af17199
            */
            $reset_key = bin2hex(openssl_random_pseudo_bytes(16));

            if ( createUniqueActivationCode($user['user_id'], $reset_key) ) {
                $to = $user['user_email'];
                $subject = 'Restablecimiento de contraseña';
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= 'From:App <noreply@app.com>' . "\r\n";
                $message = "Para restablecer su contraseña haga clic en el siguiente enlace:\n\n";
                $message .= '<p><a href="http://localhost:8000/reset_password.php?user_id='. urlencode($user['user_id']) .'&key='. urlencode($reset_key) .'">Restablecer contraseña</a></p><p>El enlace expirará en 20 minutos.</p>';
                if( mail($to, $subject , $message, $headers) ) {

                    $message = 'Enviamos un correo electrónico a <b>' . $user['user_email'] . '</b>. Haz clic en el enlace que aparece en el correo para restablecer tu contraseña.
                    Si no ves el correo electrónico en tu bandeja de entrada, revisa otros lugares donde podría estar, como tus carpetas de correo no deseado, sociales u otras.';
                } else {
                    $message = 'No pudimos enviar el correo electrónico, intentelo de nuevo más tarde.';
                }

                // render header
                require("../views/inc/header.html");

                echo '<div class="text-center" style="width: 100%;max-width: 600px;margin: auto;margin-top: 200px;text-align: justify;">
                <h3 style="font-weight: 400;">' . $message . '</h3>
                </div>';

                // render header
                require("../views/inc/footer.html");
                exit;
            }
        }
    }
}
    
// render header
require("../views/inc/header.html");
    
// render template
require("../views/user/forgot_password.html");
    
// render footer
require("../views/inc/footer.html");


// Esta función esta definida en la página de registro.
function getUserByEmail($useremail)
{
    $q = 'SELECT * FROM user WHERE user_email = ? ;';

    return Db::query($q, $useremail);
}

function createUniqueActivationCode($user_id, $reset_key)
{
    $time = time();
    $created_on = $last_modified_on = date('Y-m-d H:i:s');
    $status = 'pending';

    $q = 'INSERT INTO forgot_password (
            user_id, 
            reset_key, 
            time, 
            status, 
            created_on, 
            last_modified_on
          ) 
          VALUES (?, ?, ?, ?, ?, ?) ;';
    
    return Db::query($q, $user_id, $reset_key, $time, $status, $created_on, $last_modified_on);
}