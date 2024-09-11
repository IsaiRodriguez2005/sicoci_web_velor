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
        $consultaCP = "SELECT clave_estado FROM _cat_sat_codigos_postales WHERE codigo_postal='".$_POST['codigo']."'";
        $resultadoCP = mysqli_query($conexion, $consultaCP);
        if (mysqli_num_rows($resultadoCP) == 0) 
        {
            $options = "<option value='0'>No existe estado</option>";
        }
        else
        {
            $cp = mysqli_fetch_array($resultadoCP);

            $consultaEstado = "SELECT clave_estado, nombre_estado FROM _cat_sat_estados WHERE clave_estado='".$cp['clave_estado']."'";
            $resultadoEstado = mysqli_query($conexion, $consultaEstado);
            $estado = mysqli_fetch_array($resultadoEstado);

            $options = "<option value='".$estado['clave_estado']."'>".$estado['nombre_estado']."</option>";
        }

        echo $options;
    }
?>