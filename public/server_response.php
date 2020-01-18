<?php

require("../includes/helpers.php");
require("../src/app/Db.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (!array_key_exists('id_provincia', $_POST)) {
        echo 'ERROR: ID de parámetro URL no encontrado.';
        exit;
    }
    
    if (!isPositiveInt($_POST['id_provincia'])) {
        echo 'ERROR: Identificador de provincia inválido.';
        exit;
    }

    if ($_POST['id_provincia'] < 25) {
        
        $localidades = getLocalitiesById($_POST['id_provincia']);
        echo json_encode($localidades);
        exit;
    }

    echo 'ERROR: No pudimos procesar su solicitud.';
    exit;
}

function getLocalitiesById($id_provincia)
{
    $q = 'SELECT * FROM localidad WHERE id_provincia = ? ORDER BY nombre ;';
    
    return Db::query($q, $id_provincia);
}

function obtenerLocalidadesPorId($id_provincia){
    $q = 'SELECT * FROM localidad WHERE id_provincia = ? ORDER BY nombre ;';
    
    $localidades = Db::query($q, $id_provincia);

    echo '<option value disabled selected>Seleccione una localidad</option>';
    foreach ($localidades as $localidad) {
        echo '<option value="' . $localidad['id_localidad'] . '" ';
        if( isset($asoc['localidad']) && $asoc['localidad'] == $localidad['id_localidad'] ) echo 'selected';
        echo '>' . $localidad['nombre'] . ' ('. $localidad['cp'] . ')' . '</option>';
    }
}