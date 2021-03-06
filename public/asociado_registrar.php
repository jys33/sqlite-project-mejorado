<?php

// configuration
require("../includes/config.php");

$title = 'Alta de asociado';
$errors = [];

// set defaults
$asoc['apellido'] = $asoc['nombre'] = $asoc['nro_cuil'] = $asoc['nro_documento'] = $asoc['fech_nacimiento'] = '';
$asoc['email'] = $asoc['nro_tel_linea'] = $asoc['nro_tel_movil'] = $asoc['domicilio'] = $asoc['genero'] = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

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

    if (empty($_POST['genero'])) {
        $errors['genero'] = "Seleccione una opción.";
    } else {
        $asoc['genero'] = test_input($_POST["genero"]);
    }

    if (empty($_POST['nro_cuil'])) {
        $errors['nro_cuil'] = "Ingrese el número de cuil.";
    } else {
        $asoc['nro_cuil'] = test_input($_POST["nro_cuil"]);

        if (!validarCuit($asoc['nro_cuil'])) {
            $errors['nro_cuil'] = "El formato o el número de cuil ingresado no es válido.";
        }
    }

    if (empty($_POST['tipo_documento'])) {
        $errors['tipo_documento'] = "Seleccione una opción.";
    } else {
        $asoc['tipo_documento'] = test_input($_POST["tipo_documento"]);
    }

    if (empty($_POST['nro_documento'])) {
        $errors['nro_documento'] = "Ingrese el número de documento.";
    } else {
        $asoc['nro_documento'] = test_input($_POST["nro_documento"]);

        if (!checkFormat($regexDni, $asoc['nro_documento'])) {
            $errors['nro_documento'] = 'El formato o el número de documento ingresado no es válido.';
        }
    }

    if (empty($_POST['fech_nacimiento'])) {
        $errors['fech_nacimiento'] = "Ingrese la fecha de nacimiento.";
    } else {
        $asoc['fech_nacimiento'] = test_input($_POST["fech_nacimiento"]);

        if (!validateDate($asoc['fech_nacimiento'])) {
            $errors['fech_nacimiento'] = 'El formato o la fecha ingresada no es válida.';
        } else {
            $edadAsociado = (int) getAge($asoc['fech_nacimiento']);

            if (!($edadAsociado >= 18)) {
                $errors['fech_nacimiento'] = "Asegúrate de usar tu fecha de nacimiento real.";
            }
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
        $asoc['provincia'] = test_input($_POST["provincia"]);
    }

    if (empty($_POST['localidad'])) {
        $errors['localidad'] = "Seleccione una opción.";
    } else {
        $asoc['localidad'] = test_input($_POST["localidad"]);
    }

    if (count($errors) == 0) {

        $result = obtenerAsociadoPorNroDeCuil($asoc['nro_cuil']);

        if (count($result) == 1) {
            $errors['nro_cuil'] = 'Este número de cuil ya ha sido registrado.';
        }

        $result = obtenerAsociadoPorNroDeDni($asoc['nro_documento']);

        if (count($result) == 1) {
            $errors['nro_documento'] = 'Este número de documento ya ha sido registrado.';
        }

        $result = obtenerEmailAsociado($asoc['email']);

        if (count($result) == 1) {
            $errors['email'] = 'Este correo electrónico ya ha sido registrado.';
        }

        if (count($errors) == 0) {

            if (insertarAsociado($asoc)) {
                Flash::addFlash('La acción se realizó correctamente.');
                redirect('/');
            }
        }
    }
}

// render header
require("../views/inc/header.html");

// render template
require("../views/asociado/registrar_asociado.html");

// render footer
require("../views/inc/footer.html");

exit;

/**
 * Funciones de persistencia
 */
function obtenerAsociadoPorNroDeCuil($cuil)
{
    $q = 'SELECT * FROM asociado WHERE nro_cuil = ? ;';

    return Db::query($q, $cuil);
}

function obtenerEmailAsociado($email_asociado)
{
    $q = 'SELECT email FROM email WHERE email = ? ;';

    return Db::query($q, $email_asociado);
}

function obtenerAsociadoPorNroDeDni($dni)
{
    $q = 'SELECT * FROM asociado WHERE nro_documento = ? ;';

    return Db::query($q, $dni);
}

function insertarAsociado ($asociado) {

    $now = date('Y-m-d H:i:s');
    try {
        $db = Db::getInstance();
        // begin the transaction
        $db->beginTransaction();

        $q = 'INSERT INTO asociado ( apellido, nombre, genero, nro_cuil, tipo_documento, nro_documento, categoria, fech_nacimiento, domicilio,
        id_localidad, created_on, last_modified_on) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ;';
        Db::query($q, $asociado['apellido'], $asociado['nombre'], $asociado['genero'], $asociado['nro_cuil'], $asociado['tipo_documento'],
        $asociado['nro_documento'], $asociado['categoria'], dateToDb($asociado['fech_nacimiento']), $asociado['domicilio'], $asociado['localidad'],
        $now, $now);
    
        // Grabamos el ID del asociado insertado
        $id_asociado = Db::getInstance()->lastInsertId();
    
        $q = 'INSERT INTO email (email, id_asociado) VALUES (:email, :id_asociado);';
        Db::query($q, $asociado['email'], $id_asociado);
    
        $q = 'INSERT INTO telefono (telefono_movil, telefono_linea, id_asociado, created, last_modified) VALUES (?, ?, ?, ?, ?);';
        Db::query($q, $asociado['nro_tel_movil'], $asociado['nro_tel_linea'], $id_asociado, $now, $now);

        // commit the transaction
        $db->commit();
    } catch (PDOException $e) {
        // roll back the transaction if something failed
        $db->rollback();
        trigger_error('Error:' . $e->getMessage(), E_USER_ERROR);
        return false; // Deberíamos devolver -1 en caso de error si estuvieramos en JAVA :)
    }
    return true;
}

function obtenerTodasLasProvincias()
{
    $q = 'SELECT * FROM provincia ;';

    return Db::query($q);
}
