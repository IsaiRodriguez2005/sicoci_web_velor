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
<<<<<<< HEAD
        $html = ''; 
        $html .= '
            <table class="table table-striped" id="tabla_facturas" width="100%">
                <thead>
                    <tr>
                        <th class="sticky text-center text-sm">Acciones</th>
                        <th class="sticky text-center text-sm">Folio</th>
                        <th class="text-center text-sm">Fecha de Agenda</th>
                        <th class="text-center text-sm">Estatus</th>
                        <th class="sticky text-center text-sm">Cliente</th>
                        <th class="text-center text-sm">Terapeuta</th>
                        <th class="text-center text-sm">Consultorio</th>
                        <th class="text-center text-sm">Tipo de Cita</th>
                        <th class="text-center text-sm">Tipo de Servicio</th>
                    </tr>
                </thead>
                <tbody id="mostrar_clientes">
        ';
        
        // cosultas de busqueda
        /*
        $consulta = "SELECT 
            a.id_folio, 
            a.id_cliente, 
            a.id_consultorio, 
            a.id_terapeuta, 
            a.tipo_servicio, 
            a.tipo_cita, 
            a.fecha_emision, 
            a.fecha_agenda, 
            a.estatus, 
            a.observaciones,
            p.nombre_personal,
            c.nombre as nombre_consultorio,
            cli.nombre_cliente as nombre_cliente
            FROM emisores_agenda a 
            LEFT JOIN emisores_personal p ON a.id_terapeuta = p.id_personal AND a.id_emisor = p.id_emisor AND p.tipo = 2
            LEFT JOIN emisores_consultorios c ON a.id_consultorio = c.id_consultorio AND a.id_emisor = c.id_emisor
            LEFT JOIN emisores_clientes cli ON a.id_cliente = cli.id_cliente AND a.id_emisor = cli.id_emisor
        ";
        */
=======
>>>>>>> d75ff7c8a767d8e82e174b4601590a0de07449ba
        $consulta = " SELECT a.id_folio, 
                            a.id_cliente,
                            a.estatus,
                            a.fecha_agenda,
                            p.nombre_personal,
                            c.nombre_cliente 
                        FROM 
                            emisores_agenda a 
                            LEFT JOIN emisores_personal p ON a.id_terapeuta = p.id_personal AND a.id_emisor = p.id_emisor AND p.tipo = 2
                            LEFT JOIN emisores_clientes c ON a.id_cliente = c.id_cliente AND a.id_emisor = c.id_emisor ";
        
        $resCitas = mysqli_query($conexion, $consulta);

        $citas = array();
        if(mysqli_num_rows($resCitas) > 0){
            while($fila = mysqli_fetch_assoc($resCitas)){
                $citas[] = $fila;
            }
        }

        echo json_encode($citas);
    }
?>