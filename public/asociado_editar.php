<?php

// configuration
require("../includes/config.php");

$errors = [];
$title = 'Editar asociado';
$message = '';

// set defaults
$asoc['apellido'] = $asoc['nombre'] = $asoc['nro_cuil'] = $asoc['nro_documento'] = $asoc['fech_nacimiento'] = '';
$asoc['email'] = $asoc['nro_tel_linea'] = $asoc['nro_tel_movil'] = $asoc['domicilio'] = $asoc['genero'] = '';
$asoc['categoria'] = $asoc['id_provincia'] = $asoc['id_localidad'] = '';

/**
 * chequear si existe el ID y es un número entero positivo 
 */
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (!array_key_exists('id_asociado', $_GET)) {
        $message = 'ID de parámetro URL no encontrado.';
        require("../views/error/error.html");
        exit;
    }

    if (!isPositiveInt($_GET['id_asociado'])) {
        $message = 'Identificador de usuario inválido.';
        require("../views/error/error.html");
        exit;
    }

    $asoc = obtenerAsociadoPorId($_GET['id_asociado']);

    if (!$asoc) {
        $message = 'No pudimos procesar su solicitud.';
        require("../views/error/error.html");
        exit;
    }
    
    $asoc['fech_nacimiento'] = dateToView($asoc['fech_nacimiento']);

    $_SESSION['id_asociado'] = $asoc['id_asociado'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST['apellido'])) {
        $errors['apellido'] = 'Ingrese el apellido.';
    } else {
        $asoc['apellido'] = test_input($_POST['apellido']);

        if (!checkFormat($regexOnlyLetters, $asoc['apellido'])) {
            $errors['apellido'] = 'Solo se permiten letras (a-zA-Z), y espacios en blanco.';
        }
    }

    if (empty($_POST['nombre'])) {
        $errors['nombre'] = 'Ingrese el nombre.';
    } else {
        $asoc['nombre'] = test_input($_POST['nombre']);

        if (!checkFormat($regexOnlyLetters, $asoc['nombre'])) {
            $errors['nombre'] = 'Solo se permiten letras (a-zA-Z), y espacios en blanco.';
        }
    }

    if (empty($_POST['categoria'])) {
        $errors['categoria'] = "Seleccione una opción.";
    } else {
        $asoc['categoria'] = test_input($_POST["categoria"]);
    }

    if (empty($_POST['email'])) {
        $errors['email'] = 'Ingrese el correo electrónico.';
    } else {
        $asoc['email'] = test_input($_POST['email']);

        if (!checkFormat($regexEmail, $asoc['email'], true)) {
            $errors['email'] = '\'' . $asoc['email'] . '\' no es una dirección de correo electrónico válida.';
        }
    }

    if (!empty($_POST['nro_tel_linea'])) {
        $asoc['nro_tel_linea'] = test_input($_POST["nro_tel_linea"]);

        if (!validar_tel($asoc['nro_tel_linea'])) {
            $errors['nro_tel_linea'] = "El formato o el número de teléfono ingresado no es válido.";
        }
    } else {
        $asoc['nro_tel_linea'] = '';
    }

    if (empty($_POST['nro_tel_movil'])) {
        $errors['nro_tel_movil'] = "Ingrese el número móvil.";
    } else {
        $asoc['nro_tel_movil'] = test_input($_POST["nro_tel_movil"]);

        if (!validar_tel($asoc['nro_tel_movil'])) {
            $errors['nro_tel_movil'] = "El formato o el número de teléfono ingresado no es válido.";
        }
    }

    if (empty($_POST['domicilio'])) {
        $errors['domicilio'] = "Ingrese el domicilio.";
    } else {
        $asoc['domicilio'] = test_input($_POST["domicilio"]);
    }

    if (empty($_POST['provincia'])) {
        $errors['provincia'] = "Seleccione una opción.";
    } else {
        $asoc['id_provincia'] = test_input($_POST["provincia"]);
    }

    if (empty($_POST['localidad'])) {
        $errors['localidad'] = "Seleccione una opción.";
    } else {
        $asoc['id_localidad'] = test_input($_POST["localidad"]);
    }

    if (count($errors) == 0) {

        $asoc['id_asociado'] = $_SESSION['id_asociado'];

        if (actualizarAsociado($asoc)) {
            Flash::addFlash('La acción se realizó correctamente.');
            redirect('/');
        }
    }
}

// render header
require("../views/inc/header.html");

// render template
require("../views/asociado/editar_asociado.html");

// render footer
require("../views/inc/footer.html");

exit;

/**
 * Funciones de persistencia
 */
function obtenerAsociadoPorId($id_asociado)
{
    $q = 'SELECT a.id_asociado, a.nro_cuil, a.apellido,
            a.nombre, a.genero, a.tipo_documento,
            a.nro_documento, a.categoria, a.fech_nacimiento,
            t.telefono_movil, t.telefono_linea,
            e.email, a.domicilio, l.id_localidad, p.id_provincia
          FROM asociado a
          INNER JOIN telefono t ON a.id_asociado = t.id_asociado
          INNER JOIN localidad l ON a.id_localidad = l.id_localidad 
          INNER JOIN provincia p ON l.id_provincia = p.id_provincia 
          INNER JOIN email e ON a.id_asociado = e.id_asociado
          WHERE a.deleted = 0 AND a.id_asociado = ?;';

    $rows = Db::query($q, $id_asociado);

    if (count($rows) != 1) return false;

    return $rows[0];
}

function actualizarAsociado($asociado)
{
    $q = 'UPDATE asociado 
          SET apellido = ?, nombre = ?, categoria = ?, domicilio = ?, id_localidad = ?, last_modified_on = ? WHERE id_asociado = ? ;';

    $now = date('Y-m-d H:i:s');

    $result = Db::query($q, $asociado['apellido'], $asociado['nombre'], $asociado['categoria'], $asociado['domicilio'],
        $asociado['id_localidad'], $now, $asociado['id_asociado']);

    if (!$result) return false;

    $q = 'UPDATE email SET email = ? WHERE id_asociado = ? ;';

    $result = Db::query($q, $asociado['email'], $asociado['id_asociado']);

    if (!$result) return false;

    $q = 'UPDATE telefono SET telefono_movil = ?, telefono_linea = ?, last_modified = ? WHERE id_asociado = ? ;';

    $result = Db::query($q, $asociado['nro_tel_movil'], $asociado['nro_tel_linea'], $now, $asociado['id_asociado']);

    if (!$result) return false;

    return true;
}

function getProvinces()
{
    $q = 'SELECT * FROM provincia ;';
    return Db::query($q);
}

function getLocalities($idProvincia)
{
    $q = 'SELECT * FROM localidad WHERE id_provincia = ? ;';
    return Db::query($q, $idProvincia);
}

