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
        $consultaColonia = "SELECT clave_colonia, nombre_colonia FROM _cat_sat_colonias WHERE codigo_postal='".$_POST['codigo']."' AND estatus = 1 ORDER BY nombre_colonia ASC";
        $resultadoColonia = mysqli_query($conexion, $consultaColonia);
        if (mysqli_num_rows($resultadoColonia) == 0) 
        {
            $options ="<option value='0'>No existen colonias</option>";
        }
        else
        {
            while($colonia = mysqli_fetch_array($resultadoColonia))
            {
                $options .= "<option value='".$colonia['clave_colonia']."'>".$colonia['nombre_colonia']."</option>";
            }
        }

        echo $options;
    }
?>