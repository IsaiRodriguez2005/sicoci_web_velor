<?php

require '../../../vendor/autoload.php';
require "../../tickets/peticiones/tickets.php";
require_once '../textos/numero_a_letras.php';

use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;

//? variables necesarias 
$idEmisor = $_SESSION['id_emisor'];
$folioTicket = isset($_GET['folio_ticket']) ? intval($_GET['folio_ticket']) : null;
$idDocumento = isset($_GET['id_documento']) ? intval($_GET['id_documento']) : null;

//? array para las consultas
$datos = ['folioTicket' => $folioTicket, 'idDocumento' => $idDocumento];

//? datos primordiales del ticket
$datosTicket = obtenerTextosTicket($datos, $idEmisor, $conexion);
$productos = obtenerProductosTicket($datos, $idEmisor, $conexion);
$resEmisores = obtenerDatosDeEmisores($idEmisor, $conexion);

//? Datos del emisor
if (!$resEmisores['success']) return;
$dataEmisores = $resEmisores['data'];

$datos_emisor = '
    <b>' . $dataEmisores['nombre_social'] . '</b><br>
    <b>RFC: ' . $dataEmisores['rfc'] . '</b><br>
    <b>' . $dataEmisores['nombre_comercial'] . '</b><br>
    ' . $dataEmisores['direccion']['calle'] . ' ' . $dataEmisores['direccion']['exterior'] . ' ' . $dataEmisores['direccion']['interior'] . '<br>
    COL. ' . $dataEmisores['direccion']['colonia'] . ', CP ' . $dataEmisores['direccion']['codigo_postal'] . '<br>
    ' . $dataEmisores['direccion']['municipio'] . ', ' . $dataEmisores['direccion']['estado'] . ', ' . $dataEmisores['direccion']['pais'] . '<br>
';

// $datos_emisor = '
//     <b>' . $dataEmisores['nombre_social'] . '</b><br>
//     RFC: ' . $dataEmisores['rfc'] . '<br>
//     ' . $dataEmisores['direccion']['calle'] . ' ' . $dataEmisores['direccion']['exterior'] . ', COL. ' . $dataEmisores['direccion']['colonia'] . '<br>
//     ' . $dataEmisores['direccion']['municipio'] . ', ' . $dataEmisores['direccion']['estado'] . ', ' . $dataEmisores['direccion']['pais'] . '<br>
//     CP ' . $dataEmisores['direccion']['codigo_postal'] . '<br>
// ';

//? Folio
$folio = $datosTicket['clave_serie'] . $datosTicket['folio_ticket'];

//? configuraciones del logo y logotipo 
$resLogo = obtenerDatosLogotipo($idEmisor, $conexion);
$dataLogo = $resLogo['data'];

$logo = '';

if ($dataLogo['logo'] == 1) {

    $medidas = match ($dataLogo['tipoLogo']) {
        1 => 'width="100px" height="80px"',
        2 => 'width="120px" height="70px"',
        3 => 'width="70px" height="100px"',
        default => '',
    };
    $logoPath = '../../../emisores/' . $_SESSION['id_emisor'] . '/archivos/generales/logo.jpg';
    $logoData = base64_encode(file_get_contents($logoPath));
    $logoSrc = 'data:image/jpeg;base64,' . $logoData;

    $logo = "<img src=$logoSrc  $medidas >";
}

//? total de productos del ticket
$listProductos = '';
foreach ($productos as $producto) {
    $listProductos .= '
        <tr>
            <td align="center">' . $producto['cantidad'] . '</td>
            <td align="center">' . $producto['nombreProducto'] . '</td>
            <td align="center">$' . number_format($producto['precio'], 2) . '</td>
            <td align="center">$' . number_format($producto['descuento'], 2) . '</td>
            <td align="center">$' . number_format($producto['importe'], 2) . '</td>
        </tr>
    ';
}

//? datos de cobros
$cobrados = '';
if ($datosTicket['tcefect'] > 0) {
    $cobrados .= '
            <tr>
                <td align="left"><b>EFECTIVO COBRADO</b></td>
                <td align="right">$' . $datosTicket['tcefect'] . '</td>
            </tr>
        ';
}
if ($datosTicket['tctrans'] > 0) {
    $cobrados .= '
            <tr>
                <td align="left"><b>TRANSFERENCIA COBRADO</b></td>
                <td align="right">$' . $datosTicket['tctrans'] . '</td>
            </tr>
        ';
}

// ? total a texto
$numeroALetras = new NumeroALetras();
$totalATexto = $numeroALetras->convertir(floatval($datosTicket['total']));

$html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <div class="contenido">
        <div class="renglon">
            <div class="encabezado">
                <div class="empresa" style="margin-bottom: 20px;">
                    ' . $logo . '<br>
                    ' . $datos_emisor . '
                </div>
                <div class="datos">
                    <div align="center" class="datos_titulo">
                        NOTA DE VENTA
                    </div>
                    <div>
                        <table class="datos_parrafo">
                            <tr>
                                <td align="left"><b>FOLIO NOTA</b></td>
                                <td align="right">' . $folio . '</td>
                            </tr>
                            <tr>
                                <td align="left"><b>CLIENTE</b></td>
                                <td align="right">' . $datosTicket['nombre_cliente'] . '</td>
                            </tr>
                            <!--
                                <tr>
                                    <td align="left"><b>LUGAR EMISION</b></td>
                                    <td align="right">Ciudad, Estado, País</td>
                                </tr>
                            -->
                            <tr>
                                <td align="left"><b>FECHA EMISION</b></td>
                                <td align="right">' . $datosTicket['fecha_emision'] . '</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--
        <div class="renglon">
            <div align="center" class="titulo_receptor">
                DATOS DEL PROVEEDOR
            </div>
            <div class="datos_receptor">
                <table class="datos_parrafo">
                    <tr>
                        <td align="left"><b>PROVEEDOR</b></td>
                        <td align="left">Proveedor Ejemplo</td>
                    </tr>
                    <tr>
                        <td align="left"><b>RFC</b></td>
                        <td align="left">RFC123456789</td>
                    </tr>
                </table>
            </div>
        </div>
        -->
        <div class="renglon">
            <div align="center" class="titulo_factura">
                DETALLES DE LA COMPRA
            </div>
            <div class="datos_factura">
                <table class="datos_parrafo">
                    <tr>
                        <th>CANTIDAD</th>
                        <th>PRODUCTO</th>
                        <th>PRECIO UNITARIO</th>
                        <th>DESCUENTO</th>
                        <th>IMPORTE</th>
                    </tr>
                    ' . $listProductos . '
                </table>
            </div>
        </div>
        <div class="renglon">
            <div class="titulo_letra">
                CANTIDAD CON LETRA
            </div>
            <div class="titulo_totales">
                TOTALES
            </div>
            <div class="detalles_letra">
                <b>*** ' . $totalATexto . ' ***</b>
            </div>
            <div class="detalles_totales">
                <table class="datos_parrafo">
                    <tr>
                        <td align="left"><b>DESCUENTO</b></td>
                        <td align="right">$' . $datosTicket['total_descuento'] . '</td>
                    </tr>
                    <tr>
                        <td align="left"><b>TOTAL</b></td>
                        <td align="right">$' . $datosTicket['total'] . '</td>
                    </tr>
                    ' . $cobrados . '
                    <tr>
                        <td align="left"><b>TOTAL COBRADO</b></td>
                        <td align="right">$' . $datosTicket['total_cobrado'] . '</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
';

try {
    $mpdf = new \Mpdf\Mpdf();

    // Cargar estilos CSS
    $css = file_get_contents("../../../css/estilos_nota_venta.css");
    $mpdf->writeHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);

    // Escribir contenido HTML
    $mpdf->WriteHTML($html);

    // Salida del PDF al navegador
    $mpdf->Output('Nota_de_Venta.pdf', 'I');
} catch (\Mpdf\MpdfException $e) {
    // Mostrar el error en el navegador
    echo "Ocurrió un error al generar el PDF: " . $e->getMessage();
    die();
}




//? ESQUELETO PARA CHECAR INFO
// echo '<pre>';
// var_dump($VARIABLE);
// echo '</pre>';
// die();
