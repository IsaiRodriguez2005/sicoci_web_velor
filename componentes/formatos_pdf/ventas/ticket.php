<?php
require("../../tickets/peticiones/tickets.php");

$idEmisor = $_SESSION['id_emisor'];

$folioTicket = isset($_GET['folio_ticket']) ? intval($_GET['folio_ticket']) : null;
$idDocumento = isset($_GET['id_documento']) ? intval($_GET['id_documento']) : null;
$datos = ['folioTicket' => $folioTicket, 'idDocumento' => $idDocumento];

$datosTicket = obtenerTextosTicket($datos, $idEmisor, $conexion);
$productos = obtenerProductosTicket($datos, $idEmisor, $conexion);
$resEmisores = obtenerDatosDeEmisores($idEmisor, $conexion);
if (!$resEmisores['success']) return;
$dataEmisores = $resEmisores['data'];

//? DOMICILIO
$domicilio = $dataEmisores['direccion']['calle'] . ' #' . $dataEmisores['direccion']['exterior'];
if (!empty($dataEmisores['direccion']['interior'])) {
    $domicilio .= ' Int. ' . $dataEmisores['direccion']['interior'];
}
$domicilio .= ', Colonia ' . $dataEmisores['direccion']['colonia'] . ', ' . $dataEmisores['direccion']['municipio'] . ', ' . $dataEmisores['direccion']['estado'] . '.';
$domicilio = strtoupper($domicilio);

//? FOLIO
$folio = '#' . $datosTicket['folio_ticket'] . $datosTicket['clave_serie'];

// echo '<pre>';
// print_r(['datosTicket' => $datosTicket]);
// echo '</pre>';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Venta</title>
    <link rel="stylesheet" href="../../../css/estilos_ticket.css">
</head>

<body>
    <div style="
        width: 58mm;
        margin-top: 2.5mm;
        margin-right: 2.5mm;
        margin-bottom: 2.5mm;
        margin-left: 2.5mm;
        padding: 12px;
        font-size: 10px;
        color: #000;">
        <!-- Encabezado -->
        <div class="ticket-header">
            <!-- Espacio para el logo -->
            <div style="margin-bottom: 10px;">
                <img
                    src="../../../emisores/1/archivos/generales/logo.jpg"
                    alt="Logo del Negocio"
                    style="max-width: 120px; height: auto;">
            </div>

            <!-- Información del negocio -->
            <h1 class="business-name"><?php echo $dataEmisores['nombre_comercial'] ?? ''; ?></h1>
            <p class="business-rfc">RFC: <?php echo $dataEmisores['rfc'] ?? ''; ?></p>
            <!-- <p class="business-description">[610] - RESIDENTES EN EL EXTRANJERO SIN ESTABLECIMIENTO PERMANENTE EN MÉXICO</p> -->
            <p style="font-size: 12px; margin: 0;">
                <?php echo $domicilio ?? ''; ?>
            </p>
            <p class="business-phone">Tel: <?php echo $dataEmisores['telefono'] ?? ''; ?> </p>

        </div>

        <div class="ticket-info">
            <h2 class="ticket-title">*** TICKET DE VENTA ***</h2>
            <p class="ticket-number"><?php echo $folio ?? ''; ?></p>

            <!-- <div class="ticket-dates">
                <p><strong>FECHA CREADO:</strong> 2025-01-04 11:12:36</p>
                <p><strong>FECHA PAGADO:</strong> 2025-01-04 11:15:00</p>
            </div> -->

            <div class="ticket-client">
                <p><strong>CLIENTE:</strong> <?php echo $datosTicket['nombre_cliente'] ?? ''; ?></p>
                <p><strong>CAJERO:</strong> <?php echo $_SESSION['nombre_usuario'] ?? ''; ?></p>
            </div>
        </div>

        <!-- Línea divisoria -->
        <hr class="divider">
        <hr class="divider">
        <!-- Detalle de productos -->
        <table style="width: 100%; border-collapse: collapse; font-size: 9.5px;">
            <!-- Encabezado de la tabla -->
            <thead>
                <tr>
                    <th style="text-align: left; font-size: 9.5px; font-weight: bold;">Cant</th>
                    <th style="text-align: left; font-size: 9.5px; font-weight: bold;">Producto</th>
                    <th style="text-align: right; font-size: 9.5px; font-weight: bold;">P.Unit</th>
                    <th style="text-align: right; font-size: 9.5px; font-weight: bold;">Total</th>
                </tr>
            </thead>

            <!-- Cuerpo de la tabla -->
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td style="text-align: left; font-size: 9.5px;"><?= $producto['cantidad']; ?></td>
                        <td style="text-align: left; font-size: 9.5px;"><?= $producto['nombreProducto']; ?></td>
                        <td style="text-align: right; font-size: 9.5px;">$<?= number_format($producto['precio'], 2); ?></td>
                        <td style="text-align: right; font-size: 9.5px;">$<?= number_format($producto['cantidad'] * $producto['precio'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

            <!-- Pie de tabla: Totales -->
            <tfoot>
                <tr>
                    <td colspan="3" style="font-weight: bold; text-align: right; border-top: 1px dashed #000;">DESCUENTO</td>
                    <td style="text-align: right; border-top: 1px dashed #000;">$<?php echo $datosTicket['total_descuento'] ?? ''; ?></td>
                </tr>
                <tr>
                    <td colspan="3" style="font-weight: bold; text-align: right;">TOTAL</td>
                    <td style="text-align: right;">$<?php echo $datosTicket['total'] ?? ''; ?></td>
                </tr>

                <?php
                if ($datosTicket['tcefect'] > 0) {
                    echo '
                            <tr>
                                <td colspan="3" style="font-weight: bold; text-align: right;">EFECTIVO</td>
                                <td style="text-align: right;">$' . $datosTicket['tcefect'] . '</td>
                            </tr>
                        ';
                }
                if ($datosTicket['tctrans'] > 0) {
                    echo '
                            <tr>
                                <td colspan="3" style="font-weight: bold; text-align: right;">TRANSFERENCIA</td>
                                <td style="text-align: right;">$' . $datosTicket['tctrans'] . '</td>
                            </tr>
                        ';
                }
                ?>

                <tr>
                    <td colspan="3" style="font-weight: bold; text-align: right;">TOTAL COBRADO</td>
                    <td style="text-align: right;">$<?php echo $datosTicket['total_cobrado'] ?? ''; ?></td>
                </tr>
            </tfoot>
        </table>
        <hr class="divider">
        <hr class="divider">

        <!-- <div class="ticket-totals">
            <p><strong>PAGO:</strong></p>
            <p>$232.00</p>
        </div> -->



        <!-- Pie de página -->
        <div class="ticket-footer">
            <h3 style="font-size: 18px;">¡Gracias por su compra!</h3>
            <p style="font-size: 12px;">Visítanos nuevamente</p>
        </div>
    </div>

    <script>
        // Llama automáticamente a imprimir
        window.print();

        // Cierra la da$datosTicketna después de imprimir
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>

</html>