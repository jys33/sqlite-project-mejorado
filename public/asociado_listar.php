<?php

// configuration
require("../includes/config.php");

$title = "Lista de asociados";

$headers = ['#', 'N° de Cuil', 'Apellido', 'Nombre', 'Sexo', 'Tipo Doc.', 'N° de Doc.', 'Categoría', 'Fec. Nac.', 'Fec. Alta', 'E-mail', 'Tel. Línea', 'Tel. Móvil', 'Domicilio', 'Localidad', 'Cód. Postal', 'Provincia', 'Acciones'];

$data = obtenerTodosLosAsociados();

// render header
require("../views/inc/header.html");

// render template
require("../views/asociado/listar_asociados.html");

// render footer
require("../views/inc/footer.html");

exit;

/**
 * Funciones de persistencia
 */
function obtenerTodosLosAsociados()
{
    $q = 'SELECT a.id_asociado, a.nro_cuil, a.apellido,
          a.nombre, a.genero, a.tipo_documento,
          a.nro_documento, a.categoria, a.fech_nacimiento,
          a.created_on, e.email, a.domicilio,
          t.telefono_movil, t.telefono_linea,
          l.nombre AS localidad, l.cp, p.nombre AS provincia
        FROM asociado a 
          INNER JOIN telefono t ON a.id_asociado = t.id_asociado
          INNER JOIN localidad l ON a.id_localidad = l.id_localidad
          INNER JOIN provincia p ON l.id_provincia = p.id_provincia
          INNER JOIN email e ON a.id_asociado = e.id_asociado 
          WHERE a.deleted = 0 ORDER BY a.created_on DESC;';
    return Db::query($q);
}
