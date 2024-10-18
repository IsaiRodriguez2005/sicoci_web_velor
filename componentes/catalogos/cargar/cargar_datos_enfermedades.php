<?php
session_start();
require("../../conexion.php");

if (empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario'])) {
    session_destroy();
    echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
} else {
    $html = '';
    $html .= '
            <table class="table table-striped" id="mostrar_enfermedades" width="100%">
                <thead>
                    <tr>
                        <th class="sticky text-center text-sm">Acciones</th>
                        <th class="sticky text-center text-sm">Enferedad</th>
                        <th class="text-center text-sm">Tiempo con la enfermedad</th>
                        <th class="text-center text-sm">Toma algún medicamento</th>
                    </tr>
                </thead>
                <tbody>
        ';

    $consListEnfer = "SELECT ex.id_enfermedad AS id_enfermedad_historial, ex.tiempo_enfermedad, ex.medicamento, e.id_enfermedad AS id_enfermedad_catalogo, e.nombre
                                        FROM emisores_historial_expediente_enfermedades AS ex  
                                        INNER JOIN emisores_enfermedades AS e ON e.id_enfermedad = ex.id_enfermedad
                                        WHERE ex.id_folio = " . $_POST['id_folio_cita'] . " AND ex.id_emisor = " . $_SESSION['id_emisor'] . "";
    $resultado = mysqli_query($conexion, $consListEnfer);


    if ($resultado) {

        while ($filas = mysqli_fetch_array($resultado)) {
            $html .= "
                        <tr id='tr_" . $filas['id_enfermedad_historial'] . "'>
                            <td class='text-center'>
                                <div class='btn-group'>
                                    <button type='button' id='btn_delete_' class='btn btn-danger btn-sm' title='Eliminar cita' onclick='eliminar_enfermedad_valoracion(".$_POST['id_folio_cita'].", " . $filas['id_enfermedad_historial'] . ")'>
                                        <i class='fas fa-trash-alt'></i>
                                    </button>
                                </div>
                            </td>
                            <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>" . $filas['nombre'] . "</td>
                            <td class='text-center text-sm' style='white-space: nowrap; overflow-x: auto;'>" . $filas['tiempo_enfermedad'] . "</td>
                            <td class='text-center text-sm' id='td_ef_" . $filas['id_enfermedad_historial'] . "' style='white-space: nowrap; overflow-x: auto;'>" . $filas['medicamento'] . "</td>
                        </tr>
                    ";
        }
    }


    $html .= "
            </tbody>
        </table>
    ";
    //echo $respuesta;
    echo $html;
}

?>

<script>
    $(function() {
        $('#mostrar_enfermedades').DataTable({ // Cambié 'mostrar_enfermedades' por 'tabla_facturas'
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "responsive": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
            },
        });
    });
</script>