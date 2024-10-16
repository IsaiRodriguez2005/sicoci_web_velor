<?php
session_start();
require("../conexion.php");

if (empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario'])) {
    session_destroy();
    echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
} else {

    $html .= '
    <table class="table table-striped" id="tabla_facturas">
        <thead>
            <tr>
                <th class="sticky text-center text-sm">Acciones</th>
                <th class="sticky text-center text-sm">Serie</th>
                <th class="sticky text-center text-sm">Folio</th>
                <th class="text-center text-sm">Fecha</th>
                <th class="text-center text-sm">Estatus</th>
                <th class="text-center text-sm">Cobranza</th>
                <th class="text-center text-sm">Cliente</th>
                <th class="text-center text-sm">UUID</th>
                <th class="text-center text-sm">Total</th>
                <th class="text-center text-sm">Referencia</th>
                <th class="text-center text-sm">Document&oacute;</th>
            </tr>
        </thead>
        <tbody id="mostrar_clientes">
    ';
    // revisamos la fecha de la agenda, para sacar los cosltorios que estan en ese dia y hora ocupados
    $consultorios_Activos = "SELECT id_consultorio FROM emisores_agenda WHERE fecha_agenda = '" . $_POST['fecha_hora'] . "'";
    $resultado = mysqli_query($conexion, $consultorios_Activos);
    $filas = mysqli_fetch_assoc($resultado);
    // validamos si hay alguno
    if ($filas) {

        // si es que hubo, vamos ahacr una busqueda en emisores_personal para descartar los cosltorios que esten ocuados en ese dia y hora
        $consultaTerapeuta = "SELECT * FROM emisores_consultorios WHERE id_consultorio != ".$filas['id_consultorio']." ORDER BY nombre ASC";
        $resTerapeutas = mysqli_query($conexion, $consultaTerapeuta);
        while ($consultorio = mysqli_fetch_array($resTerapeutas)) {
            // cargamos los cosltorios que estan disponibles
            $html.= "<option value='" . $consultorio['id_consultorio'] . "'>" . $consultorio['nombre'] . "</option>";
        }

        echo $html;
    } 
    // si no hay ningun consultorio a esa hora, cargaremos todos 
    else 
    {
        $consultaTerapeuta = "SELECT * FROM emisores_consultorios ORDER BY nombre ASC";
        $resTerapeutas = mysqli_query($conexion, $consultaTerapeuta);
        while ($consultorio = mysqli_fetch_array($resTerapeutas)) {
            $html.= "<option value='" . $consultorio['id_consultorio'] . "'>" . $consultorio['nombre'] . "</option>";
        }

        echo $html;
    }
}
?>
<?php
    session_start();
    require("../conexion.php");
    date_default_timezone_set('America/Mexico_City');

    if(empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario']))
    {
        session_destroy();
        echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
    }
    else
    {
       
        $consulta = "
            SELECT 
                f.id_documento, f.folio_factura, f.serie_factura, f.fecha_emision, f.estatus, f.estatus_cobranza, f.nombre, f.uuid, f.total, f.referencia, u.nombre AS usuario FROM emisores_facturas f INNER JOIN usuarios u ON u.id_usuario = f.id_usuario AND u.id_emisor = f.id_emisor WHERE f.id_emisor = ".$_SESSION['id_emisor']." AND f.fecha_emision >= '".$_POST['finicial']."' AND f.fecha_emision <= '".$_POST['ffinal']."' ORDER BY f.folio_factura DESC";
        $resFacturas = mysqli_query($conexion, $consulta);
        while($facturas = mysqli_fetch_array($resFacturas))
        {
            switch($facturas['estatus'])
            {
                case 1:
                    $estatus = '<span class="badge badge-danger" style="width: 100%; color:white;">APERTURADO</span>';
                    $boton_xml = 'disabled';
                    $boton_pdf = 'disabled';
                    $$boton_editar = '';
                    $boton_enviar = 'disabled';
                    $boton_cancelar = '';
                    break;
                case 2:
                    $estatus = '<span class="badge badge-warning" style="width: 100%; color:white;">PROFORMA</span>';
                    $boton_xml = 'disabled';
                    $boton_pdf = '';
                    $$boton_editar = '';
                    $boton_enviar = '';
                    $boton_cancelar = '';
                    break;
                case 3:
                    $estatus = '<span class="badge badge-success" style="width: 100%; color:white;">TIMBRADO</span>';
                    $boton_xml = '';
                    $boton_pdf = '';
                    $$boton_editar = 'disabled';
                    $boton_enviar = '';
                    $boton_cancelar = '';
                    break;
                case 4:
                    $estatus = '<span class="badge badge-light" style="width: 100%; color:white;">EN CANCELACION</span>';
                    $boton_xml = '';
                    $boton_pdf = '';
                    $$boton_editar = 'disabled';
                    $boton_enviar = '';
                    $boton_cancelar = '';
                    break;
                case 5:
                    $estatus = '<span class="badge badge-secondary" style="width: 100%; color:white;">CANCELADO</span>';
                    $boton_xml = '';
                    $boton_pdf = '';
                    $$boton_editar = 'disabled';
                    $boton_enviar = '';
                    $boton_cancelar = 'disabled';
                    break;
            }
            switch($facturas['estatus_cobranza'])
            {
                case 1:
                    $estatus_cobranza = '<span class="badge badge-danger" style="width: 100%; color:white;">POR COBRAR</span>';
                    break;
                case 2:
                    $estatus_cobranza = '<span class="badge badge-primary" style="width: 100%; color:white;">PAGADO</span>';
                    break;
            }
            $html .="
                <tr id='tr_".$facturas['id_documento'].$facturas['folio_factura']."'>
                    <td class='text-center'>
                        <div class='btn-group'>
                            <button type='button' id='btn_xml_".$facturas['id_documento'].$facturas['folio_factura']."' class='btn btn-secondary btn-sm' ".$boton_xml." title='Descargar XML' onclick='descargar_xml(".$facturas['id_documento'].",".$facturas['folio_factura'].")'>
                                <i class='fas fa-code'></i>
                            </button>
                            &nbsp;
                            <button type='button' id='btn_pdf_".$facturas['id_documento'].$facturas['folio_factura']."' class='btn btn-primary btn-sm' ".$boton_pdf." title='Ver PDF' onclick='ver_pdf(".$facturas['id_documento'].",".$facturas['folio_factura'].",&quot;".$facturas['serie_factura']."&quot;)'>
                                <i class='fas fa-copy'></i>
                            </button>
                            &nbsp;
                            <button type='button' id='btn_edit_".$facturas['id_documento'].$facturas['folio_factura']."' class='btn btn-warning btn-sm' ".$boton_editar." title='Editar factura' onclick='editar_factura(".$facturas['id_documento'].",".$facturas['folio_factura'].",&quot;".$facturas['serie_factura']."&quot;)'>
                                <i class='fas fa-pencil-alt'></i>
                            </button>
                            &nbsp;
                            <button type='button' id='btn_envio_".$facturas['id_documento'].$facturas['folio_factura']."' class='btn btn-success btn-sm' ".$boton_enviar." title='Enviar archivos' onclick='evniar_correo(".$facturas['id_documento'].",".$facturas['folio_factura'].")'>
                                <i class='fas fa-paper-plane'></i>
                            </button>
                            &nbsp;
                            <button type='button' id='btn_can_".$facturas['id_documento'].$facturas['folio_factura']."' class='btn btn-danger btn-sm' ".$boton_cancelar." title='Cancelar factura' onclick='cancelar_factura(".$facturas['id_documento'].",".$facturas['folio_factura'].")'>
                                <i class='fas fa-ban'></i>
                            </button>
                        </div>
                    </td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".$facturas['serie_factura']."</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".$facturas['folio_factura']."</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".date("d/m/Y", strtotime($facturas['fecha_emision']))."</td>
                    <td class='text-center text-sm' id='td_ef_".$facturas['id_documento'].$facturas['folio_factura']."' style='white-space: nowrap; overflow-x: auto;'>".$estatus."</td>
                    <td class='text-center text-sm' id='td_ec_".$facturas['id_documento'].$facturas['folio_factura']."' style='white-space: nowrap; overflow-x: auto;'>".$estatus_cobranza."</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".$facturas['nombre']."</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".$facturas['uuid']."</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".$facturas['total']."</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".$facturas['referencia']."</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".$facturas['usuario']."</td>
                </tr>
            ";
        }
        $html .= "
                </tbody>
            </table>
        ";

        echo $html;
    }
?>
<script>
  $(function () {
    $('#tabla_facturas').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": true,
      "responsive": true,
      "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
        }
    });
  });
</script>