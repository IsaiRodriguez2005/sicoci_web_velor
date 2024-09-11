<?php
    session_start();
    require("../conexion.php");

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
        $consultaForma = "SELECT clave_forma, descripcion FROM _cat_sat_forma_pago WHERE clave_forma <> '99' AND estatus = 1 ORDER BY descripcion ASC";
        $resultadoForma = mysqli_query($conexion, $consultaForma);
        $options .= "<option value='0'>Forma de pago</option>";
        while($forma = mysqli_fetch_array($resultadoForma))
        {
            $options .= "<option value='".$forma['clave_forma']."'>[".$forma['clave_forma']."] ".$forma['descripcion']."</option>";
        }

        echo $options;
    }
?>