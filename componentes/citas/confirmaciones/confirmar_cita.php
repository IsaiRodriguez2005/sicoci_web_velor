<?php
session_start();
require("../../conexion.php");
date_default_timezone_set('America/Mexico_City');

if (empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario'])) {
    session_destroy();
} else {

    //* Validar la solicitud según el método (GET o POST)

    //* si existe get, quieredecir que viene de correo
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (!empty($_GET)) {
            confirmacionPorCorreo($_GET, $conexion);
        } else {
            echo "Error: No se proporcionaron parámetros en la solicitud GET.";
        }

    //* si existe post, quiere decir que viene del sistema 
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty($_POST)) {
            if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['nombre_usuario'])) {
                echo "Error: Sesión no válida. No puedes realizar esta acción.";
            } else {
                confirmacionEnSistema($_POST, $conexion);
            }
        } else {
            echo "Error: No se proporcionaron parámetros en la solicitud POST.";
        }
    } else {
        echo "Error: Método de solicitud no permitido.";
    }

    exit;
}

function confirmacionEnSistema($post, $conexion)
{
    if (isset($post['folio_cita'], $post['id_terapeuta'])) {
        $idTerapeuta = $post['id_terapeuta'];
        $folioCita = $post['folio_cita'];
        $idEmisor = $_SESSION['id_emisor'];

        $sqlConfirmacion = "UPDATE emisores_agenda SET conf_ct_ter = 1 WHERE id_folio = ? AND id_terapeuta = ? AND id_emisor = $idEmisor;";
        $confStmt = mysqli_prepare($conexion,  $sqlConfirmacion);
        mysqli_stmt_bind_param($confStmt, 'ii', $folioCita, $idTerapeuta);
        $respuesta = mysqli_stmt_execute($confStmt);
        if ($respuesta) {
            echo 'ok';
        } else {
            echo 'Error al conformar la cita. Por favor intentelo de nuevo';
        }
    } else {
        echo 'error';
    }
}

function confirmacionPorCorreo($get, $conexion)
{

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

    if (isset($get['id_terapeuta'], $get['folio_cita'])) {

        $idTerapeuta = $get['id_terapeuta'];
        $folioCita = $get['folio_cita'];

        $sqlTeraCita = "SELECT p.id_personal as id_terapeuta, 
                                        p.nombre_personal as nombre, 
                                        a.id_folio as folio  
                                FROM emisores_agenda a INNER JOIN emisores_personal p on a.id_terapeuta = p.id_personal 
                                WHERE a.id_folio = ? AND a.id_terapeuta = ?;";
        $stmt = mysqli_prepare($conexion, $sqlTeraCita);
        mysqli_stmt_bind_param($stmt, 'ii', $folioCita, $idTerapeuta);
        mysqli_stmt_execute($stmt);
        $respuesta = mysqli_stmt_get_result($stmt);
        $datos = mysqli_fetch_array($respuesta);

        if ($datos) {

            $sqlConfirmacion = "UPDATE emisores_agenda SET conf_ct_ter = 1 WHERE id_folio = ? AND id_terapeuta = ?;";
            $confStmt = mysqli_prepare($conexion,  $sqlConfirmacion);
            mysqli_stmt_bind_param($confStmt, 'ii', $folioCita, $idTerapeuta);
            $respuesta = mysqli_stmt_execute($confStmt);
            if ($respuesta) {
                renderPaginaConfirmacion($datos['folio']);
            } else {
                echo 'Error al conformar la cita. Por favor intentelo de nuevo';
            }
        } else {
            echo 'No se encontro el folio de la cita. Por favor comuniate con soporte.';
        }
    }
}

function renderPaginaConfirmacion($folio)
{
    //* esta funcion renderiza la pagina, osea, solo si es por correo
?>
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
        <div class="card text-center">
            <div class="card-header">
                Confirmación Exitosa
            </div>
            <div class="card-body">
                <h5 class="card-title">¡Gracias por confirmar la cita!</h5>
                <p class="card-text">La cita con el paciente ha sido confirmada exitosamente.</p>
                <p><strong>Folio de la cita:</strong> <?php echo $folio; ?></p>
                <!-- <a href="agenda.php" class="btn btn-primary">Volver a la Agenda</a> -->
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>

<?php
}
?>