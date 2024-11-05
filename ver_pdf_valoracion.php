<?php
session_start();
date_default_timezone_set('America/Mexico_City');
//error_reporting(0);
require("componentes/conexion.php");
require './vendor/autoload.php';

use Mpdf\Mpdf;
use Mpdf\HTMLParserMode;


if (empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario'])) {
    session_destroy();
    echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
} else {


    $nombre_comercial = $_SESSION['nombre_comercial'];
    $id_documento = 1;
    $folio_valoracion = $_GET['id_folio'];

    //* Datos de valoracion
    $datosValoracion = 'SELECT ex.*,
                                ag.id_folio,
                                ag.id_consultorio,
                                ag.id_terapeuta,
                                p.nombre_personal,
                                c.nombre_cliente,
                                c.telefono,
                                c.correo,
                                o.nombre_ocupacion
                                FROM emisores_historial_expediente AS ex 
                                        LEFT JOIN emisores_agenda AS ag ON ag.id_folio = ex.id_folio_cita
                                        LEFT JOIN emisores_personal AS p ON p.id_personal = ag.id_terapeuta
                                        LEFT JOIN emisores_clientes AS c ON c.id_cliente = ex.id_cliente
                                        LEFT JOIN emisores_ocupaciones AS o ON o.id_ocupacion = ex.id_ocupacion
                                WHERE ex.id_emisor = ' . $_SESSION['id_emisor'] . ' AND ex.folio = ' . $folio_valoracion . ';';
    $resultado = mysqli_query($conexion, $datosValoracion);
    $dataVal = mysqli_fetch_array($resultado);

    $antecedentes = '';

    $datosEnfermedades = 'SELECT ee.id_folio, ee.id_enfermedad, ee.tiempo_enfermedad, ee.medicamento, e.nombre FROM emisores_historial_expediente_enfermedades AS ee
                                    LEFT JOIN emisores_enfermedades AS e ON ee.id_enfermedad = e.id_enfermedad                 
                            WHERE ee.id_folio = ' . $dataVal['id_folio_cita'] . ' AND ee.id_emisor = ' . $_SESSION['id_emisor'] . ';';
    $resultado = mysqli_query($conexion, $datosEnfermedades);

    //* Antecedentes de enfermedades (referenciando a el expediente del paciente)
    $antecedentes .= '<div class="renglon">
                        <div class="titulo_receptor">
                            DATOS BASICOS
                        </div>
                        <div class="datos_receptor">
                            <table class="datos_parrafo" width="100%">
                                <tr>
                                    <th align="left">ENFERMEDAD</th>
                                    <th align="left">TIEMPO CON LA ENFERMEDAD</th>
                                    <th align="left">MEDICAMENTOS</th>
                                </tr>
        ';
    if ($resultado) {
        while ($enfermedades = mysqli_fetch_array($resultado)) {
            //print_r($enfermedades);
            $antecedentes .= '<tr>
                                <td>' . $enfermedades['nombre'] . '</td>
                                <td>' . $enfermedades['tiempo_enfermedad'] . '</td>
                                <td>' . $enfermedades['medicamento'] . '</td>
                            </tr>';
        }
    };
    $antecedentes .= '
                            </table>
                        </div>
                    </div>';

    //* Esacala de valor
    // $escalaValHTML = '';

    // for ($i = 0; $i < $dataVal['escala_eva']; $i++) {
    //     $escalaValHTML .= '<td class="filled escala-dolor" align="center"></td>';
    // }

    // if ($dataVal['escala_eva'] < 10) {
    //     for ($dataVal['escala_eva']; $dataVal['escala_eva'] < 10; $dataVal['escala_eva']++) {
    //         $escalaValHTML .= '<td class="filled" align="center"></td>';
    //     }
    // }
    switch ($dataVal['escala_eva']) {
        case 1:
            $color = '#D4EDDA'; // Verde claro
            break;
        case 2:
            $color = '#A9DFBF'; // Verde
            break;
        case 3:
            $color = '#7DCEA0'; // Verde oliva
            break;
        case 4:
            $color = '#F9E79F'; // Amarillo verdoso
            break;
        case 5:
            $color = '#F4D03F'; // Amarillo
            break;
        case 6:
            $color = '#F39C12'; // Naranja claro
            break;
        case 7:
            $color = '#E67E22'; // Naranja oscuro
            break;
        case 8:
            $color = '#E74C3C'; // Rojo claro
            break;
        case 9:
            $color = '#CB4335'; // Rojo intenso
            break;
        case 10:
            $color = '#922B21'; // Rojo oscuro
            break;
        default:
            $color = '#FFFFFF'; // Color por defecto (blanco)
            break;
    }



    //* estado civil
    if ($dataVal['estado_civil'] == 1) {
        $estadoCivil = 'Soltero/a';
    } else {
        $estadoCivil = 'Casado/a';
    }

    $serie_factura = '';
    $d_tipo_factura_pdf = 0;


    //CONSULTAMOS DATOS DEL EMISOR
    $sql_emisor = "SELECT 
            e.rfc, 
            e.nombre_social, 
            e.calle, 
            e.exterior, 
            e.interior, 
            e.codigo_postal,
            IF(LENGTH(e.clave_colonia) > 4, e.clave_colonia, c.nombre_colonia) AS colonia, 
            m.nombre_municipio,
            e.clave_estado,
            e.clave_pais,
            e.clave_regimen,
            e.correo,
            e.telefono,
            rf.descripcion as nombre_regimen
            FROM emisores e
            LEFT JOIN _cat_sat_colonias c ON c.clave_colonia = e.clave_colonia AND c.codigo_postal = e.codigo_postal
            LEFT JOIN _cat_sat_municipios m ON m.clave_municipio = e.clave_municipio AND m.clave_estado = e.clave_estado
            LEFT JOIN _cat_sat_regimen_fiscal rf ON e.clave_regimen = rf.clave_regimen 
            WHERE e.id_emisor = " . $_SESSION['id_emisor'];
    $res_emisor = mysqli_query($conexion, $sql_emisor);
    $emisor = mysqli_fetch_array($res_emisor);
    $datos_emisor = '
            <b>' . $emisor['nombre_social'] . '</b><br>
            <b>RFC: ' . $emisor['rfc'] . '</b><br>
            <b>[' . $emisor['clave_regimen'] . '] - ' . $emisor['nombre_regimen'] . '</b><br>
            ' . $emisor['calle'] . ' ' . $emisor['exterior'] . ' ' . $emisor['interior'] . ' COL. ' . $emisor['colonia'] . ' CP ' . $emisor['codigo_postal'] . ', ' . $emisor['nombre_municipio'] . ', ' . $emisor['clave_estado'] . ', ' . $emisor['clave_pais'] . '<br>
            
        ';

    //CARGAMOS CONFIGURACION DEL LOGOTIPO
    $sql_con = "SELECT logo, tipo_logo, marca, tipo_marca FROM emisores_configuraciones WHERE id_emisor = " . $_SESSION['id_emisor'];
    $res_con = mysqli_query($conexion, $sql_con);
    $configuraciones = mysqli_fetch_array($res_con);
    if ($configuraciones['logo'] == 1) {
        switch ($configuraciones['tipo_logo']) {
            case 1:
                $medidas = 'width="100px" height="80px"';
                break;
            case 2:
                $medidas = 'width="120px" height="70px"';
                break;
            case 3:
                $medidas = 'width="70px" height="100px"';
                break;
        }

        $logo = '<img src="emisores/' . $_SESSION['id_emisor'] . '/archivos/generales/logo.jpg" ' . $medidas . '>';
        // $logo = '"emisores/' . $_SESSION['id_emisor'] . '/archivos/generales/logo.jpg" ' . $medidas . '';
    } else {
        $logo = '';
    }

    $residencia = '';
    $detalle = '';
    $etiqueta_cancelado = '';

    $qr = '';
    $mensaje_footer = '';


    //ARMO EL HTML
    $html = '
            <body>
                <div class="contenido">
                    <div class="renglon">
                        <div class="encabezado">
                            <div class="empresa">
                                ' . $logo . '<br>
                                ' . $datos_emisor . '
                            </div>
                            <div class="datos">
                                <div class="datos_titulo">
                                    REGISTRO ' . $nombre_comercial . '
                                </div>
                                <div>
                                    <table width="100%" class="datos_parrafo">
                                        <tr>
                                            <td width="50%" align="left"><b>FOLIO: </b></td>
                                            <td align="right" >' . $folio_valoracion . '</td>
                                        </tr>
                                        <tr>
                                            <td width="50%" align="left"><b>FECHA EMISI&Oacute;N: </b></td>
                                            <td align="right">' . $dataVal['fecha_emision'] . '</td>
                                        </tr>
                                        <tr>
                                            <td width="50%" align="left"><b>FISIO</b></td>
                                            <td align="right">' . $dataVal['nombre_personal'] . '</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="renglon">
                        <div class="titulo_receptor">
                            DATOS BASICOS
                        </div>
                        <div class="datos_receptor">
                            <table class="datos_parrafo" width="100%">
                                <tr>
                                    <td width="15%" align="left"><b>NOMBRE:</b></td>
                                    <td width="35%" align="left">' . $dataVal['nombre_cliente'] . '</td>
                                    <td width="20%" align="left"><b>RÉGIMEN FISCAL:</b></td>
                                    <td width="30%" align="left">' . $dataVal['regimen_fiscal'] . '</td>
                                </tr>
                                <tr>
                                    <td align="left"><b>EDAD:</b></td>
                                    <td align="left">' . $dataVal['edad'] . '</td>
                                    <td align="left"><b>TELÉFONO:</b></td>
                                    <td align="left">' . $dataVal['telefono'] . '</td>
                                </tr>
                                <tr>
                                    <td align="left"><b>OCUPACIÓN:</b></td>
                                    <td align="left">' . $dataVal['nombre_ocupacion'] . '</td>
                                    <td align="left"><b>ESTADO CIVIL:</b></td>
                                    <td align="left">' . $estadoCivil . '</td>
                                </tr>
                                <tr>
                                    <td align="left"><b>CORREO:</b></td>
                                    <td align="left">' . $dataVal['correo'] . '</td>
                                    <td align="left"><b>TOXICOMANÍAS:</b></td>
                                    <td align="left">' . $dataVal['taxicomanias'] . '</td>
                                </tr>
                                <tr>
                                    <td align="left"><b>MOTIVO DE CONSULTA:</b></td>
                                    <td colspan="3" align="left">' . $dataVal['motivo_consulta'] . '</td>
                                </tr>
                                <tr>
                                    <td align="left"><b>ACTIVIDAD FÍSICA:</b></td>
                                    <td colspan="3" align="left">' . $dataVal['actividad_fisica'] . '</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    ' . $antecedentes . '
                    <div class="renglon">
                        <div class="encabezado">
                            <div class="datos_receptor">
                                <div class="titulo_receptor">
                                    SIGNOS VITALES
                                </div>
                                <table class="datos_parrafo" width="100%">
                                    <tr>
                                        <td width="15%" align="left"><b>TENCIÓN ARTERIAL:</b></td>
                                        <td width="35%" align="left">' . $dataVal['ta'] . '</td>
                                        <td width="20%" align="left"><b>FC:</b></td>
                                        <td width="30%" align="left">' . $dataVal['fc'] . '</td>
                                    </tr>
                                    <tr>
                                        <td align="left"><b>FR:</b></td>
                                        <td align="left">' . $dataVal['fr'] . '</td>
                                        <td align="left"><b>SATURACIÓN DE O2:</b></td>
                                        <td align="left">' . $dataVal['oxigeno'] . '</td>
                                    </tr>
                                    <tr>
                                        <td align="left"><b>TEMPERATURA:</b></td>
                                        <td align="left">' . $dataVal['temperatura'] . '</td>
                                        <td align="left"><b>GLUCOSA:</b></td>
                                        <td align="left">' . $dataVal['glucosa'] . '</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="datos">
                                <div class="escala-valor">
                                    <div class="titulo_receptor">
                                        ESCALA DE EVA
                                    </div>
                                    <div class="escala-valor-num">
                                        <div class="contenedor">
                                            <div class="numero">' . $dataVal['escala_eva'] . '</div>
                                            <div class="cuadro_inferior" style="background-color: '.$color.';"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="renglon">
                        <div class="titulo_receptor">
                            F&Aacute;RMACOS
                        </div>
                        <div class="datos_receptor">
                            <table class="datos_parrafo" width="100%">
                                <tr>
                                    <td width="35%" align="center">' . $dataVal['farmacos'] . '</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="renglon">
                        <div class="titulo_receptor">
                            DIAGN&Oacute;STICO M&Eacute;DICO
                        </div>
                        <div class="datos_receptor">
                            <table class="datos_parrafo" width="100%">
                                <tr>
                                    <td width="35%" align="center">' . $dataVal['diagnostico_medico'] . '</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
            <!--
                    <div class="renglon">
                        <div class="titulo_factura">
                            CONCEPTOS DE LA FACTURA
                        </div>
                        <div class="datos_factura">
                            <table width="100%" class="datos_parrafo" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="5%" align="center" style="border-bottom: 1px solid;"><b>CAN</b></td>
                                    <td width="10%" align="center" style="border-bottom: 1px solid;"><b>CLAVE UNIDAD</b></td>
                                    <td width="10%" align="center" style="border-bottom: 1px solid;"><b>CLAVE CONCEPTO</b></td>
                                    <td width="35%" align="center" style="border-bottom: 1px solid;"><b>DESCRIPCION</b></td>
                                    <td width="10%" align="center" style="border-bottom: 1px solid;"><b>P / U</b></td>
                                    <td width="10%" align="center" style="border-bottom: 1px solid;"><b>IVA</b></td>
                                    <td width="10%" align="center" style="border-bottom: 1px solid;"><b>RET IVA</b></td>
                                    <td width="10%" align="center" style="border-bottom: 1px solid;"><b>IMPORTE SIN IMP</b></td>
                                </tr>
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
                            <b>*** ***</b>
                        </div>
                        <div class="detalles_totales">
                            <table width="100%" class="datos_parrafo">
                                <tr>
                                    <td width="50%" align="left"><b>SUBTOTAL</b></td>
                                    <td width="50%" align="right">$ </td>
                                </tr>
                                <tr>
                                    <td width="50%" align="left"><b>+ IVA</b></td>
                                    <td width="50%" align="right">$</td>
                                </tr>
                                <tr>
                                    <td width="50%" align="left"><b>- RET IVA</b></td>
                                    <td width="50%" align="right">$ </td>
                                </tr>
                                <tr>
                                    <td width="50%" align="left"><b>- RET ISR</b></td>
                                    <td width="50%" align="right"></td>
                                </tr>
                                <tr>
                                    <td width="50%" align="left"><b>TOTAL</b></td>
                                    <td width="50%" align="right"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="renglon">
                        <div class="titulo_complementarios">
                            DATOS COMPLEMENTARIOS
                        </div>
                        <div class="detalles_complementarios">
                            <table width="100%" class="datos_parrafo">
                                <tr>
                                    <td width="17%" align="left"><b>VERSION CFDI</b></td>
                                    <td width="33%" align="left"></td>
                                    <td width="15%" align="left"><b>TIPO</b></td>
                                    <td width="35%" align="left">[I] - INGRESO</td>
                                </tr>
                                <tr>
                                    <td align="left"><b>USO CFDI</b></td>
                                    <td align="left"></td>
                                    <td align="left"><b>CP EXPEDICION</b></td>
                                    <td align="left"></td>
                                </tr>
                                <tr>
                                    <td align="left"><b>METODO PAGO</b></td>
                                    <td align="left"></td>
                                    <td align="left"><b>FORMA PAGO</b></td>
                                    <td align="left"></td>
                                </tr>
                                
                            </table>
                        </div>
                    </div>
                    <div class="renglon">
                        <div class="qr">
                            ' . $qr . '
                        </div>
                        <div class="cadenas">
                        
                        </div>
                    </div>
            -->

                </div>
            </body>
        ';
    $footer = '
            <div class="footer">
                ' . $mensaje_footer . '<br>
                DOCUMENTO ELABORADO MEDIANTE VELOR FACTURACION | www.velor.mx | P&aacute;gina: {PAGENO}/{nbpg}
            </div>
        ';

    $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');

    $mpdf = new Mpdf();
    $css = file_get_contents("css/estilos_factura.css");
    $mpdf->SetHTMLFooter($footer);
    $mpdf->writeHTML($css, HTMLParserMode::HEADER_CSS);
    $mpdf->writeHTML($html, HTMLParserMode::HTML_BODY);


    $mpdf->output('Vlaora' . ".pdf", "I");
}
