<?php

// configuration
require("../includes/config.php");

if (!empty($_SESSION["user_id"])) {
    redirect("/");
}

$title = "Registro de usuario";
$errors = [];
$user['last_name'] = $user['first_name'] = $user['user_name'] = $user['user_email'] = $user['password'] = $user['confirm_password'] = '';

if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {

    if (empty($_POST['last_name'])) {
        $errors['last_name'] = 'Ingrese su apellido.';
    } else {
        $user['last_name'] = test_input( $_POST['last_name'] );

        if(!checkFormat($regexOnlyLetters, $user['last_name'])) {
	        $errors['last_name'] = 'Solo se permiten letras (a-zA-Z), y espacios en blanco.';
	    }
    }

    if (empty($_POST['first_name'])) {
        $errors['first_name'] = 'Ingrese su nombre.';
    } else {
        $user['first_name'] = test_input( $_POST['first_name'] );

        if(!checkFormat($regexOnlyLetters, $user['first_name'])) {
	        $errors['first_name'] = 'Solo se permiten letras (a-zA-Z), y espacios en blanco.';
	    }
    }

    if (empty($_POST['user_name'])) {
        $errors['user_name'] = 'Ingrese su nombre de usuario.';
    } else {
        $user['user_name'] = test_input( $_POST['user_name'] );

        if(!checkLength($user['user_name'], $minUsernameLength, $maxUsernameLength) ){
            $errors['user_name'] = 'Su nombre de usuario debe tener entre ' . $minUsernameLength . ' y ' . $maxUsernameLength . ' caracteres.';
        } elseif(!checkFormat($regexUser, $user['user_name'])) {
            $errors['user_name'] = 'Solo se permiten letras (a-z), números (0-9) y guiones(-, _).';
        }
    }

    if (empty($_POST['user_email'])) {
        $errors['user_email'] = 'Ingrese su correo electrónico.';
    } else {
        $user['user_email'] = test_input( $_POST['user_email'] );

        if ( !checkFormat($regexEmail, $user['user_email'], true) ) {
            $errors['user_email'] = '\''. $user['user_email'] . '\' no es una dirección de correo electrónico válida.';
        }
    }

    $validPassword = false;
    if (empty($_POST['password'])) {
        $errors['password'] = 'Ingrese su contraseña.';
    } else {
        $user['password'] = test_input( $_POST['password'] );

        if(!checkLength($user['password'], $minPasswordLength, $maxPasswordLength) ){
            $errors['password'] = 'Su contraseña debe tener entre ' . $minPasswordLength . ' y ' . $maxPasswordLength . ' caracteres.';
        } elseif( !validatePasswordStrength($user['password']) ){
            $errors['password'] = 'La contraseña debe incluir al menos una letra mayúscula, un número y un carácter especial.';
        } else {
            $validPassword = true;
        }
    }

    if (empty($_POST['confirm_password'])) {
        $errors['confirm_password'] = 'Confirme su contraseña.';
    } else {
        $user['confirm_password'] = test_input( $_POST['confirm_password'] );

        if ($validPassword) {
            // Comparación segura a nivel binario sensible a mayúsculas y minúsculas.
            if (strcmp($user['password'], $user['confirm_password']) !== 0) {
                $errors['confirm_password'] = 'Las contraseñas que ingresó no coinciden.';
            }
        }
    }

    // Si no hay errores
    if ( count($errors) == 0 ) {

        $res = getUserByUsername($user['user_name']);

        if ( count($res) == 1 ){
            $errors['user_name'] = 'Este nombre de usuario ya esta en uso.';
        }

        $res = getUserByEmail($user['user_email']);

        if ( count($res) == 1 ){
            $errors['user_email'] = 'Este correo electrónico ya esta registrado.';
        }

        if (count($errors) == 0) {
            
            // Generamos un código de activación
            $user['activation_key'] = bin2hex(openssl_random_pseudo_bytes(16));
            $user['password'] = encryptPassword($user['password']);
    
            if ( createUser($user) ) {
                $to = $user['user_email'];
                $subject = 'Activación de cuenta';
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= 'From:Casa del Maestro <noreply@app.com>' . "\r\n";
                $message = "Para activar su cuenta haga clic en el siguiente enlace:\n\n";
                $message .= '<p><a href="http://localhost:8000/user_activate.php?email='. urlencode($user['user_email']) .'&key='. urlencode($user['activation_key']) .'">Activar mi cuenta</a></p>';
                if( mail($to, $subject , $message, $headers) ) {
                    Flash::addFlash('Se ha enviado un enlace para activar su cuenta a ' . $user['user_email'] . '.');
                    redirect('/');
                }
            }
        }
    }
}
    
// render header
require("../views/inc/header.html");
    
// render template
require("../views/user/register.html");
    
// render footer
require("../views/inc/footer.html");

/**
 * Funciones de persistencia
 */
function getUserByUsername($username)
{
    $q = 'SELECT * FROM user WHERE user_name = ?;';

    return Db::query($q, $username);
}

function createUser($user)
{
    $q = 'INSERT INTO user (
            last_name,
            first_name,
            user_email,
            user_name,
            PASSWORD,
            activation,
            created_on,
            last_modified_on
          ) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?);';
          
    $created_on = $last_modified_on = date('Y-m-d H:i:s');

    return Db::query(
        $q,
        $user['last_name'], $user['first_name'], $user['user_email'], $user['user_name'], $user['password'], $user['activation_key'], $created_on, $last_modified_on
    );
}