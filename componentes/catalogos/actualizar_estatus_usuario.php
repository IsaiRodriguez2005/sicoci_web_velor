<?php
    session_start();
    require("../conexion.php");

    include '../correos/enviar_correo.php';
    
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
        $sqlEmisor = "UPDATE usuarios SET estatus=".$_POST['estatus']." WHERE id_emisor = ".$_SESSION['id_emisor']." AND id_usuario=".$_POST['id_usuario'];
        mysqli_query($conexion, $sqlEmisor);

        if(mysqli_affected_rows($conexion) > 0){
            $sqlUsuario = 'SELECT u.nombre,
                                    u.correo,
                                    e.nombre_comercial, 
                                    e.nombre_social 
                                    FROM usuarios u 
                                    LEFT JOIN emisores e ON u.id_emisor = e.id_emisor
                                    WHERE u.id_emisor = '.$_SESSION['id_emisor'].' 
                                    AND u.id_usuario = '.$_POST['id_usuario'].';';
            echo $sqlUsuario;
            $resultado = mysqli_query($conexion, $sqlUsuario);

            while ($res = mysqli_fetch_array($resultado)) {
                if ($res['correo']) {
                    $asunto = 'Que tal ' . strtoupper($res['nombre']) . '.';

                    if(intval($_POST['estatus']) == 1){
                        $mensaje = '
                            Cosera notificador: <br><br>
                            <b>El administrador de la empresa '.$res['nombre_comercial'].' ha activado tu perfil.</b> <br>
                            <br>
                            PD. Este correo es informativo por lo que no es necesario responder dicho correo.
                            ';
                    } else {
                        $mensaje = '
                                Cosera notificador: <br><br>
                                <b>El administrador de la empresa '.$res['nombre_comercial'].' ha suspendido tu perfil.</b> <br>
                                <br>
                                PD. Este correo es informativo por lo que no es necesario responder dicho correo.
                                ';
                    }
                    enviarCorreo($res['correo'], $asunto, $mensaje);
                }
            }

        }
    }
?>