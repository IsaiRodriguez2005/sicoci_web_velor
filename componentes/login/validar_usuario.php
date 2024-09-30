<?php
    session_start();
    require("../conexion.php");
    
    if(empty($_POST['correo']) || empty($_POST['password']))
    {
        session_destroy();
        echo "error";
    }
    else
    {
        //Consulta para validar el usuario
        if($_POST['pin'] == '0')
        {
            $sqlUsuario = "SELECT id_usuario, id_emisor, nombre,'' AS nombre_comercial FROM usuarios WHERE id_emisor = 0 AND correo = '".$_POST['correo']."' AND password = '".$_POST['password']."' AND estatus = 1";
        }
        else
        {
            $sqlUsuario = "SELECT u.id_usuario, u.id_emisor, u.nombre, e.nombre_comercial, c.sello_vigencia FROM usuarios u LEFT JOIN emisores e ON e.id_emisor = u.id_emisor LEFT JOIN emisores_configuraciones c ON c.id_emisor = u.id_emisor WHERE u.id_emisor=".$_POST['pin']." AND u.correo='".$_POST['correo']."' AND u.password='".$_POST['password']."' AND u.estatus = 1 AND e.estatus = 1";
        }
        

        $resUsuario = mysqli_query($conexion, $sqlUsuario);
        $usuario = mysqli_fetch_array($resUsuario);
        
        if (mysqli_num_rows($resUsuario) == 0) 
        {
            
            session_destroy();
            echo "error";
        }
        else
        {
            if($_POST['pin'] != 0)
            {
                $_SESSION['sello'] = $usuario['sello_vigencia'];
            }
            else
            {
                $_SESSION['sello'] = "";
            }
            if($_POST['pin'] != 0)
            {
                $_SESSION['nombre_comercial'] = $usuario["nombre_comercial"];
            }
            else
            {
                $_SESSION['nombre_comercial'] = "";
            }
            $_SESSION['id_usuario'] = $usuario["id_usuario"];
            // $_SESSION['nombre_comercial'] = $usuario["nombre_comercial"];
            $_SESSION['nombre_usuario'] = $usuario["nombre"];
            $_SESSION['id_emisor'] = $usuario["id_emisor"];
            echo "correcto";
        }
    }
?>