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
        $consultaCP = "SELECT clave_estado, clave_municipio FROM _cat_sat_codigos_postales WHERE codigo_postal='".$_POST['codigo']."'";
        $resultadoCP = mysqli_query($conexion, $consultaCP);
        if (mysqli_num_rows($resultadoCP) == 0) 
        {
            $options = "<option value='0'>No existe municipio</option>";
        }
        else
        {
            $cp = mysqli_fetch_array($resultadoCP);

            $consultaMunicipio = "SELECT clave_municipio, nombre_municipio FROM _cat_sat_municipios WHERE clave_estado='".$cp['clave_estado']."' AND clave_municipio='".$cp['clave_municipio']."'";
            $resultadoMunicipio = mysqli_query($conexion, $consultaMunicipio);
            $municipio = mysqli_fetch_array($resultadoMunicipio);

            $options = "<option value='".$municipio['clave_municipio']."'>".$municipio['nombre_municipio']."</option>";
        }

        echo $options;
    }
?>