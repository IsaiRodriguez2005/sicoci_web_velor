<?php
    session_start();
    require("../../conexion.php");

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
        $html = '
            <table class="table table-striped" id="tabla_cliente" width="100%">
                <thead>
                    <tr>
                        <th class="sticky text-center">Acciones</th>
                        <th class="sticky text-center">ID</th>
                        <th class="text-center">Nombre del Cliente/Paciente</th>
                        <th class="text-center">Estatus</th>
                    </tr>
                </thead>
                <tbody id="mostrar_clientes">
        ';
                $consultaClientes = "SELECT * FROM emisores_clientes WHERE id_emisor = ".$_SESSION['id_emisor']." ORDER BY nombre_cliente ASC";
                $resClientes = mysqli_query($conexion, $consultaClientes);
                while($clientes = mysqli_fetch_array($resClientes))
                {
                    $reemplazo = array("&", '"', "'");
                    $caracteres = array("&amp;", "&quot;", "&apos;");
                    $nuevo_social = str_replace($caracteres, $reemplazo, $clientes['nombre_cliente']);

                    if($clientes['estatus'] == 1)
                    {
                        $estado = "Activo";
                        $titulo = "Desactivar cliente";
                        $color = "btn-secondary";
                        $desactive = "<i class='fas fa-ban'></i>";
                        $codigo_estatus = 2;
                        $disabled_edicion = "";
                    }else{
                        $estado = "Inactivo";
                        $titulo = "Activar cliente";
                        $color = "btn-success";
                        $desactive = "<i class='fas fa-check'></i>";
                        $codigo_estatus = 1;
                        $disabled_edicion = "disabled";
                    }
                    $html .= "
                        <tr>
                            <td class='text-center'>
                                <div class='btn-group' id='div-check".$clientes['id_cliente']."'>
                                    <button type='button' class='btn btn-primary btn-sm' ".$disabled_edicion." title='Perfiles de facturaci&oacute;n' onclick='cargar_perfil(".$clientes['id_cliente'].", &quot;".$clientes['nombre_cliente']."&quot;)'>
                                        <i class='fas fa-dollar-sign'></i>
                                    </button>
                                    &nbsp;
                                    <button type='button' class='btn btn-warning btn-sm' ".$disabled_edicion." title='Editar registro' onclick='editar_cliente(".$clientes['id_cliente'].", &quot;".$clientes['nombre_cliente']."&quot;, &quot;".$clientes['correo']."&quot;, &quot;".$clientes['telefono']."&quot;, &quot;".$clientes['fec_nac']."&quot;, ".$clientes['ocupacion'].", , ".$clientes['est_civ'].")'>
                                        <i class='fas fa-edit'></i>
                                    </button>
                                    &nbsp;
                                    <button type='button' class='btn ".$color." btn-sm' title='".$titulo."' onclick='actualizar_estatus_cliente(".$clientes['id_cliente'].",".$codigo_estatus.");'>
                                        ".$desactive."
                                    </button>
                                </div>
                            </td>
                            <td class='text-center'>".$clientes['id_cliente']."</td>
                            <td class='text-center'>".$clientes['nombre_cliente']."</td>
                            <td class='text-center'>".$estado."</td>
                        </tr>
                    ";
                }
        $html .='
                </tbody>
            </table>
        ';
        
        echo $html;
    }
?>
<script>
  $(function () {
    $('#tabla_cliente').DataTable({
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