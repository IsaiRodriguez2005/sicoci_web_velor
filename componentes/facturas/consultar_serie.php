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
        $sql = "SELECT COUNT(*) as conteo FROM emisores_series WHERE id_emisor = ".$_SESSION['id_emisor']." AND id_documento = 1 AND estatus = 1";
        $res = mysqli_query($conexion, $sql);
        $datos = mysqli_fetch_array($res);

        if($datos['conteo'] > 0)
        {
            $html .= '
                <table class="table table-striped" id="tabla_series">
                    <thead>
                        <tr>
                            <th class="sticky text-center">Acciones</th>
                            <th class="text-center">Documento</th>
                            <th class="text-center">Serie</th>
                            <th class="text-center">Folio</th>
                            <th class="text-center">Lugar Emisi&oacute;n</th>
                        </tr>
                    </thead>
                    <tbody>
            ';

            $serie = "SELECT e.id_partida, e.id_documento, e.serie, e.folio, e.codigo_postal, CONCAT(m.nombre_municipio, ', ', cp.clave_estado) AS lugar_emision FROM emisores_series e INNER JOIN _cat_sat_codigos_postales cp ON cp.codigo_postal = e.codigo_postal INNER JOIN _cat_sat_municipios m ON m.clave_municipio = cp.clave_municipio AND m.clave_estado = cp.clave_estado WHERE e.id_emisor = ".$_SESSION['id_emisor']." AND e.estatus = 1";
            $res = mysqli_query($conexion, $serie);
            while($catalogo = mysqli_fetch_array($res))
            {
                $html .= "
                    <tr>
                        <td class='text-center'>
                            <div class='btn-group'>
                                <button type='button' class='btn btn-primary btn-sm' title='Seleccionar serie' onclick='aperturar_factura(".$catalogo['id_partida'].", ".$catalogo['id_documento'].")'>
                                    <i class='fas fa-arrow-alt-circle-down'></i>
                                </button>
                            </div>
                        </td>
                        <td class='text-center'>FACTURA</td>
                        <td class='text-center'>".$catalogo['serie']."</td>
                        <td class='text-center'>".$catalogo['folio']."</td>
                        <td class='text-center'>".$catalogo['lugar_emision']."</td>
                    </tr>
                ";
            }

            $html .= '
                    </tbody>
                </table>
            ';

            echo $html;
        }
        else
        {
            echo "error";
        }
    }
?>