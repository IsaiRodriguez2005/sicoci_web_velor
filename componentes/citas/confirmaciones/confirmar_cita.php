<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>confirmaciones</h1>
</body>
</html>

<?php
    require("../../conexion.php");
    date_default_timezone_set('America/Mexico_City');

    if(isset($_GET['id_cliente'])){
        $idCliente = $_GET['id_cliente'];

        $sqlCliente = "SELECT nombre_cliente as nombre FROM emisores_clientes WHERE id_cliente = $idCliente;";
        $respesta = mysqli_query($conexion, $sqlCliente);
        $cliente = mysqli_fetch_row($respesta);
        var_dump($cliente);
        echo $idCliente;

        if($cliente){

        }
    }

    if(isset($_GET['id_terapeuta'])){
        $idTerapeuta = $_GET['id_terapeuta'];

        $sqlTerapeuta = "SELECT nombre_personal as nombre FROM emisores_personal WHERE id_personal = $idTerapeuta;";
        $respesta = mysqli_query($conexion, $sqlTerapeuta);
        $terapeuta = mysqli_fetch_row($respesta);
        var_dump($terapeuta);
        echo $idTerapeuta;

        if($terapeuta){
            
        }
    }

?>