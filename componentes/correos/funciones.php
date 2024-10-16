<?php

function obtenerFechaEspaniol($fecha_hora)
{
    $fecha = new DateTime($fecha_hora);

    $numero_dia = $fecha->format('d');
    $dia = $fecha->format('l');
    $mes = $fecha->format('F');
    $anio = $fecha->format('Y');
    $hora_12 = $fecha->format('h:i A'); // Formato 12 horas

    $dias_en_espanol = [
        'Sunday' => 'Domingo',
        'Monday' => 'Lunes',
        'Tuesday' => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday' => 'Jueves',
        'Friday' => 'Viernes',
        'Saturday' => 'Sábado',
    ];

    $meses_en_espanol = [
        'January' => 'Enero',
        'February' => 'Febrero',
        'March' => 'Marzo',
        'April' => 'Abril',
        'May' => 'Mayo',
        'June' => 'Junio',
        'July' => 'Julio',
        'August' => 'Agosto',
        'September' => 'Septiembre',
        'October' => 'Octubre',
        'November' => 'Noviembre',
        'December' => 'Diciembre',
    ];

    $nombre_dia_español = $dias_en_espanol[$dia];
    $nombre_mes_español = $meses_en_espanol[$mes];

    return [
        'dia' => $nombre_dia_español,
        'mes' => $nombre_mes_español,
        'anio' => $anio,
        'hora' => $hora_12,
        'num_dia' => $numero_dia,
    ];
}


function getDatosClientes($id, $conex)
{
    // consulta para saber si hay correo 
    $consulCliente = "SELECT nombre_cliente, correo FROM emisores_clientes WHERE id_cliente = $id ";
    $resCliente = mysqli_query($conex, $consulCliente);
    $datosCliente = mysqli_fetch_array($resCliente);

    return $datosCliente;
}

function getDatosTerapeuta($id, $conex)
{

    $consulNomTerap = "SELECT nombre_personal, correo FROM emisores_personal WHERE id_personal = $id AND tipo = 2";
    $resTerap = mysqli_query($conex, $consulNomTerap);
    $datosTerap = mysqli_fetch_array($resTerap);

    return $datosTerap;
}

function getDatosConsultorio($id, $conex)
{
    $consulConsultorio = "SELECT nombre FROM emisores_consultorios WHERE id_consultorio = $id ";
    $resConsultorio = mysqli_query($conex, $consulConsultorio);
    $datosConsultorio = mysqli_fetch_array($resConsultorio);

    return $datosConsultorio;
}

function getDatosAgenda($id, $conex)
{
    $consulAgenda = "SELECT * FROM emisores_agenda WHERE id_folio = $id";
    $resAgenda = mysqli_query($conex, $consulAgenda);
    $datosAgenda = mysqli_fetch_array($resAgenda);

    return $datosAgenda;
}

function getDatosPermisos($id, $conex){

    $consultaPermiso = "SELECT * FROM emisores_personal_permisos WHERE id_permiso = $id";
    $resPermiso = mysqli_query($conex, $consultaPermiso);
    $datosAgenda = mysqli_fetch_array($resPermiso);

    return $datosAgenda;
}