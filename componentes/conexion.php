<?php
    //* pruebas--------------------
    //$conexion = mysqli_connect("localhost","kyosoftm_cosera_user","7o5Lmr4TDn*]","kyosoftm_cosera_pruebas");
    
    //* local----------------------
    $conexion = mysqli_connect("localhost","root","","cosera");
    
    //* productivo----------------
    //$conexion = mysqli_connect("localhost","kyosoftm_cosera_user","7o5Lmr4TDn*]","kyosoftm_cosera");

function ejecutarConsultaPreparada($conexion, $query, $tipos, $parametros) {
    $stmt = mysqli_prepare($conexion, $query);
    if (!$stmt) {
        return [
            'success' => false,
            'error' => 'Error al preparar la consulta: ' . mysqli_error($conexion)
        ];
    }
    
    mysqli_stmt_bind_param($stmt, $tipos, ...$parametros);

    if (!mysqli_stmt_execute($stmt)) {
        $error = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        return [
            'success' => false,
            'error' => 'Error al ejecutar la consulta: ' . $error
        ];
    }

    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    return [
        'success' => true,
        'data' => $result
    ];
}

?>