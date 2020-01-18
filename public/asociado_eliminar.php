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
    '/register.php'
];

// PHP_SELF: /asociado_eliminar.php
if ( !in_array($_SERVER["PHP_SELF"], $pages) ) {
    
    if (empty($_SESSION["user_id"])) {
        redirect("login.php");
    }
}

$message = '';
$title = 'NOT FOUND';

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
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

    if(eliminarAsociado( $_GET['id_asociado'] ) ){
        $message = 'El registro se eliminó correctamente.';
        Flash::addFlash($message);
    } else {
        $message = 'No pudimos procesar su solicitud.';
        require("../views/error/error.html");
        exit;
    }
}

redirect('/');

/**
 * Funciones de persistencia
 */
function eliminarAsociado($id_asociado)
{
    /**
     * Habilitamos la eliminación ON DELETE CASCADE
     * para base de datos SQLite
     */
    Db::getInstance()->exec('PRAGMA foreign_keys = ON ;');

    $q = 'DELETE 
          FROM
            asociado 
          WHERE id_asociado = ? ;';

    return Db::query($q, $id_asociado);
}