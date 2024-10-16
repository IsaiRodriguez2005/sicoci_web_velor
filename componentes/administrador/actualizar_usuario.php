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
        $sqlEmisor = "UPDATE usuarios SET id_emisor=".$_POST['emisor'].", nombre='".strtoupper($_POST['nombre'])."', correo='".$_POST['correo']."', password='".$_POST['password']."' WHERE id_usuario=".$_POST['id_usuario'];
        mysqli_query($conexion, $sqlEmisor);
        
        if($_POST['emisor'] == 0)
        {
            $sqlBorrar = "DELETE FROM usuarios_permisos WHERE id_usuario = ".$_POST['id_usuario'];
            mysqli_query($conexion, $sqlBorrar);
        }
        else
        {
            $sqlExiste = "SELECT COUNT(*) AS contar FROM usuarios_permisos WHERE id_usuario = ".$_POST['id_usuario'];
            $resExiste = mysqli_query($conexion, $sqlExiste);
            $existe = mysqli_fetch_array($resExiste);

            if($existe['contar'] == 0)
            {
                $insertPermisos = "INSERT INTO usuarios_permisos VALUES('#', ".$_POST['emisor'].", ".$_POST['id_usuario'].", 1, 1, 1, 1, 1, 1, 1, 1)";
                mysqli_query($conexion, $insertPermisos);
            }
        }
    }
?>