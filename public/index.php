<?php

// configuration
require("../includes/config.php");

$title = "Lista de asociados";

$headers = ['#', 'N° de cuil', 'Apellido', 'Nombre', 'Género', 'Du', 'N° de documento', 'Categoría', 'Fech. de nacimiento', 'Fech. de ingreso', 'E-mail', 'Tel. línea', 'Tel. móvil', 'Domicilio', 'Localidad', 'Cód. postal', 'Provincia', 'Acciones'];

$data = obtenerTodosLosAsociados();

foreach ($data as $key => $asociado) {
    $tel_movil = obtenerTelAsociado($asociado['id_asociado']);
    $data[$key]['nro_tel_movil'] = $tel_movil['nro_tel'] ?? '';
    $tel_linea = obtenerTelAsociado($asociado['id_asociado'], 'linea');
    $data[$key]['nro_tel_linea'] = $tel_linea['nro_tel'] ?? '';
}

// render header
require("../views/inc/header.html");

// render template
require("../views/asociado/listar_asociados.html");

// render footer
require("../views/inc/footer.html");

/**
 * Funciones de persistencia
 */
function obtenerTodosLosAsociados()
{
    $q = 'SELECT
            a.id_asociado,
            a.nro_cuil,
            a.apellido,
            a.nombre,
            a.genero,
            a.tipo_documento,
            a.nro_documento,
            a.categoria,
            a.fech_nacimiento,
            a.created_on,
            e.email,
            a.domicilio,
            l.nombre AS localidad,
            l.cp,
            p.nombre AS provincia
          FROM asociado a
          JOIN localidad l
          ON a.id_localidad = l.id_localidad
          JOIN provincia p
          ON l.id_provincia = p.id_provincia
          JOIN email e
          ON a.id_asociado = e.id_asociado ORDER BY a.created_on DESC;';

    return Db::query($q);
}

/**
 * funcion repetida en asociado editar
 */
function obtenerTelAsociado($id_asociado, $tipo = 'movil')
{
    $q = 'SELECT
            nro_tel
          FROM
            telefono
          WHERE  tipo = ?
          AND id_asociado = ? ;';

    $stmt = Db::getInstance()->prepare($q);

    if($stmt === false) return false;

    $result = $stmt->execute([$tipo, $id_asociado]);

    if (!$result) return false;

    return $stmt->fetch(PDO::FETCH_ASSOC);
}