<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Cita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../css/estilos_correos_conf.css">

</head>

<body class="bg-light">

    <?php
    require("../../conexion.php");
    date_default_timezone_set('America/Mexico_City');

    // if (isset($_GET['id_cliente'])) {
    //     $idCliente = $_GET['id_cliente'];

    //     $sqlCliente = "SELECT nombre_cliente as nombre FROM emisores_clientes WHERE id_cliente = $idCliente;";
    //     $respesta = mysqli_query($conexion, $sqlCliente);
    //     $cliente = mysqli_fetch_row($respesta);
    //     var_dump($cliente);
    //     echo $idCliente;

    //     if ($cliente) {
    //     }
    // }

    if (isset($_GET['id_terapeuta'])) {
        $idTerapeuta = $_GET['id_terapeuta'];
        $folioCita = $_GET['folio_cita'];

        $sqlTeraCita = "SELECT p.id_personal as id_terapeuta, 
                                p.nombre_personal as nombre, 
                                a.id_folio as folio  
                        FROM emisores_agenda a INNER JOIN emisores_personal p on a.id_terapeuta = p.id_personal 
                        WHERE a.id_folio = $folioCita AND a.id_terapeuta = $idTerapeuta;";
        $respuesta = mysqli_query($conexion, $sqlTeraCita);
        $datos = mysqli_fetch_array($respuesta);

        if ($datos) {
            $sqlConfirmacion = "UPDATE emisores_agenda SET conf_ct_ter = 1 WHERE id_folio = $folioCita AND id_terapeuta = $idTerapeuta;";
            $respuesta = mysqli_query($conexion, $sqlConfirmacion);
            if ($respuesta) {
    ?>
                <div class="card text-center">
                    <div class="card-header">
                        Confirmación Exitosa
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">¡Gracias por confirmar la cita!</h5>
                        <p class="card-text">La cita con el paciente ha sido confirmada exitosamente.</p>
                        <p><strong>Folio de la cita:</strong> <?php echo $datos['folio']; ?></p>
                        <!-- <a href="agenda.php" class="btn btn-primary">Volver a la Agenda</a> -->
                    </div>
                </div>
    <?php
            }
        }
    }

    ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>