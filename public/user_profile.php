<?php

// configuration
require("../includes/config.php");

$errors = [];

$title = 'Mi perfil';

$user['last_name'] = $user['first_name'] = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
    if (empty($_POST['last_name'])) {
        $errors['last_name'] = 'Ingrese su apellido.';
    } else {
        $user['last_name'] = test_input($_POST['last_name']);

        if (!checkFormat($regexOnlyLetters, $user['last_name'])) {
            $errors['last_name'] = 'Solo se permiten letras (a-zA-Z), y espacios en blanco.';
        }
    }

    if (empty($_POST['first_name'])) {
        $errors['first_name'] = 'Ingrese su nombre.';
    } else {
        $user['first_name'] = test_input($_POST['first_name']);

        if (!checkFormat($regexOnlyLetters, $user['first_name'])) {
            $errors['first_name'] = 'Solo se permiten letras (a-zA-Z), y espacios en blanco.';
        }
    }

    // Si no hay errores
    if (count($errors) == 0) {
    }
}

$rows = getUserById((int) $_SESSION['user_id']);

if (count($rows) == 1) {
    $user = $rows[0];
} else {
    $errors['current_user'] = 'El usuario no existe.';
}

// render header
require("../views/inc/header.html");

// render template
require("../views/user/profile.html");

// render footer
require("../views/inc/footer.html");

exit;

/**
 * Funciones de persistencia
 */
