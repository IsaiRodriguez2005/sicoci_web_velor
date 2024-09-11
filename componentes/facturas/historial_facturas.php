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
        $html = ''; 
        $html .= '
            <table class="table table-striped table-responsive" id="tabla_facturas">
                <thead>
                    <tr>
                        <th class="sticky text-center text-sm">Acciones</th>
                        <th class="sticky text-center text-sm">Folio</th>
                        <th class="text-center text-sm">Fecha de Emisión</th>
                        <th class="text-center text-sm">Fecha de Agenda</th>
                        <th class="text-center text-sm">Estatus</th>
                        <th class="sticky text-center text-sm">Cliente</th>
                        <th class="text-center text-sm">Terapeuta</th>
                        <th class="text-center text-sm">Consultorio</th>
                        <th class="text-center text-sm">Tipo de Cita</th>
                        <th class="text-center text-sm">Tipo de Servicio</th>
                    </tr>
                </thead>
                <tbody id="mostrar_clientes">
        ';
        
        // cosultas de busqueda
        $consulta = "SELECT * FROM emisores_agenda ";
        

        if(!empty($_POST)) // si {$_POST} contiene algo, ejecutara las validaciones
        {
            // agregamos el {WHERE} a la consulta
            $consulta .= "WHERE ";
            
            // primera validacion, si hay {cliente}
            if(!empty($_POST['id_cliente'])){

                $consulta .= " id_cliente = ".$_POST['id_cliente'];
            }

            // segunda validacin, si es que hay {estatus}
            if(!empty($_POST['estatus'])){

                // si el cliente existio antes, pondremos el {AND} 
                if(!empty($_POST['id_cliente'])) $consulta .= " AND"; 
                
                $consulta .= " estatus = ".$_POST['estatus'];
            }

            // tercera validacion, si hay {terapeuta}
            if(!empty($_POST['id_terapeuta'])){

                // si el cliente o estatus existio antes, pondremos el {AND} 
                if(!empty($_POST['estatus']) || !empty($_POST['id_cliente'])) $consulta .= " AND ";

                $consulta .= " id_terapeuta = ".$_POST['id_terapeuta'];
            }

            // cuarta validacion, si hay {consultorio}
            if(!empty($_POST['id_consultorio'])){

                // si el cliente o estatus o terapeuta existio antes, pondremos el {AND} 
                if(!empty($_POST['id_terapeuta']) || !empty($_POST['estatus']) || !empty($_POST['id_cliente'])) $consulta .= " AND ";

                $consulta .= " id_consultorio = ".$_POST['id_consultorio'];
            }

            // RANGO DE FECHAS -------------------------------------BETWEEN---------------------------------

            // sexta validacion, si hay {fecha de agenda}
            if(!empty($_POST['fecha_inicial'])){

                // si el cliente o estatus o terapeuta o consultoio existio antes, pondremos el {AND} 
                if(!empty($_POST['id_terapeuta']) || !empty($_POST['estatus']) || !empty($_POST['id_cliente']) || !empty($_POST['id_consultorio'])) $consulta .= " AND ";

                // agregamos el {BETWEEN}
                $consulta .= ' fecha_agenda BETWEEN '; 
                

                $consulta .= "'".$fecha_inicial. "' AND '". $fecha_final."'";
            }


            // cerramos la consulta
            $consulta .= " ORDER BY fecha_agenda ASC;";
        }
        
        $resCitas = mysqli_query($conexion, $consulta);
        while($citas = mysqli_fetch_array($resCitas))
        {
            switch($citas['estatus'])
            {
                case 1:
                    $estatus = '<span class="badge badge-danger" style="width: 100%; color:white;">APERTURADO</span>';
                    $boton_xml = 'disabled';
                    $boton_pdf = 'disabled';
                    $boton_editar = '';
                    $boton_cancelar = '';
                    break;
                case 2:
                    $estatus = '<span class="badge badge-warning" style="width: 100%; color:white;">AGENDADO</span>';
                    $boton_xml = 'disabled';
                    $boton_editar = '';
                    $boton_cancelar = '';
                    break;
                case 3:
                    $estatus = '<span class="badge badge-success" style="width: 100%; color:white;">REALIZADO</span>';
                    $boton_xml = '';
                    $boton_editar = 'disabled';
                    $boton_cancelar = '';
                    break;
                case 4:
                    $estatus = '<span class="badge badge-light" style="width: 100%; color:white;">REPROGRAMADO</span>';
                    $boton_xml = '';
                    $boton_editar = 'disabled';
                    $boton_cancelar = '';
                    break;
                case 5:
                    $estatus = '<span class="badge badge-secondary" style="width: 100%; color:white;">CANCELADO</span>';
                    $boton_xml = '';
                    $boton_editar = 'disabled';
                    $boton_cancelar = 'disabled';
                    break;
            }

            switch($citas['tipo_cita'])
            {
                case 1:
                    $tipo_cita = 'SEGUIMIENTO';
                break;

                case 2: 
                    $tipo_cita = 'PRIMERA VEZ';
            }

            switch($citas['tipo_servicio'])
            {
                case 1:
                    $tipo_servicio = 'CONSULTORIO';
                break;

                case 2: 
                    $tipo_servicio = 'DOMICILIO';
            }

            // Consulta de cliente
            $consulta_cliente = "SELECT nombre_social FROM emisores_clientes WHERE id_cliente = ".$citas['id_cliente'].";";
            $res_cliente = mysqli_query($conexion, $consulta_cliente);
            $res_cliente = mysqli_fetch_array($res_cliente);

            // Consulta de terapeuta
            $consulta_terapeuta = "SELECT nombre_personal FROM emisores_personal WHERE id_personal = ".$citas['id_terapeuta']." AND tipo = 2;";
            $res_terapeuta = mysqli_query($conexion, $consulta_terapeuta);
            $res_terapeuta = mysqli_fetch_array($res_terapeuta);

            // Consulta de onsultorio
            $consulta_consultorio = "SELECT nombre FROM emisores_consultorios WHERE id_consultorio = ".$citas['id_consultorio'].";";
            $res_consultorio = mysqli_query($conexion, $consulta_consultorio);
            $res_consultorio = mysqli_fetch_array($res_consultorio);

            $html .="
                <tr id='tr_".$citas['id_folio']."'>
                    <td class='text-center'>
                        <div class='btn-group'>
                            <button type='button' id='btn_edit_".$citas['id_folio']."' class='btn btn-warning btn-sm' ".$boton_editar." title='Editar cita' onclick='editar_cita(".$citas['id_folio'].");')'>
                                <i class='fas fa-pencil-alt'></i>
                            </button>
                            &nbsp;
                            <button type='button' id='btn_xml_".$citas['id_folio']."' class='btn btn-secondary btn-sm' ".$boton_xml." title='Reagendar cita' onclick='descargar_xml(".",".$citas['id_folio'].")'>
                                <i class='fas fa-code'></i>
                            </button>
                            &nbsp;
                            <button type='button' id='btn_can_".$citas['id_folio']."' class='btn btn-danger btn-sm' ".$boton_cancelar." title='Cancelar cita' onclick='cancelar_factura(".",".$citas['id_folio'].")'>
                                <i class='fas fa-ban'></i>
                            </button>
                        </div>
                    </td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".$citas['id_folio']."</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".date("d/m/Y", strtotime($citas['fecha_emision']))."</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".date("d/m/Y", strtotime($citas['fecha_agenda']))."</td>
                    <td class='text-center text-sm' id='td_ef_".$citas['id_folio']."' style='white-space: nowrap; overflow-x: auto;'>".$estatus."</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".$res_cliente['nombre_social']."</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".$res_terapeuta['nombre_personal']."</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".$res_consultorio['nombre']."</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".$tipo_cita."</td>
                    <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>".$tipo_servicio."</td>
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