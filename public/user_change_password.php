<?php

// configuration
require("../includes/config.php");

$errors = [];

$title = 'Cambiar contraseña';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (empty($_POST['current_password'])) {
        $errors['current_password'] = 'Ingrese su contraseña actual.';
    } else {
        $user['current_password'] = test_input($_POST['current_password']);

        $rows = getUserById((int) $_SESSION['user_id']);

        if (count($rows) == 1) {
            $row = $rows[0];
            if (!verifyPassword($user['current_password'], $row['password'])) {
                $errors['current_password'] = 'Su contraseña actual no es correcta.';
            }
        } else {
            $errors['current_password'] = 'El usuario no existe.';
        }
    }

    $validPassword = false;
    if (empty($_POST['new_password'])) {
        $errors['new_password'] = 'Ingrese su nueva contraseña.';
    } else {
        $user['new_password'] = test_input($_POST['new_password']);

        if (!checkLength($user['new_password'], $minPasswordLength, $maxPasswordLength)) {
            $errors['new_password'] = 'Su contraseña debe tener entre ' . $minPasswordLength . ' y ' . $maxPasswordLength . ' caracteres.';
        } elseif (!validatePasswordStrength($user['new_password'])) {
            $errors['new_password'] = 'La contraseña debe incluir al menos una letra mayúscula, un número y un carácter especial.';
        } else {
            $validPassword = true;
        }
    }

    if (empty($_POST['confirm_new_password'])) {
        $errors['confirm_new_password'] = 'Confirme su nueva contraseña.';
    } else {
        $user['confirm_new_password'] = test_input($_POST['confirm_new_password']);

        if ($validPassword) {
            // Comparación segura a nivel binario sensible a mayúsculas y minúsculas.
            if (strcmp($user['new_password'], $user['confirm_new_password']) !== 0) {
                $errors['confirm_new_password'] = 'Las contraseñas no coinciden.';
            }
        }
    }

    if (count($errors) == 0) {
        $user['new_password'] = encryptPassword($user['new_password']);

        if (updateUserPassword($user['new_password'], $_SESSION['user_id'])) {
            Flash::addFlash('Su contraseña ha sido actualizada correctamente.');

            redirect('/');
        }
    }
}


// render header
require("../views/inc/header.html");

// render template
require("../views/user/change_password.html");

// render footer
require("../views/inc/footer.html");

exit;

/**
 * Funciones de persistencia
 */
function updateUserPassword($password, $user_id)
{
    $q = 'UPDATE user
          SET
            password = ?
          WHERE
            user_id = ? ';

    return Db::query($q, $password, $user_id);
}
