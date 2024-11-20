function select_tipo_desc() {
    //* dependiendo de la seleccion del 'tipo' de descento, se deshabilitaran los campos.

    tipo = $("#tipo").val();

    if (Number(tipo) == 1) {
        $("#pct_consul").attr('disabled', true);
        $("#cost_consul").removeAttr('disabled');
    } else if (Number(tipo) == 2) {
        $("#cost_consul").attr('disabled', true);
        $("#pct_consul").removeAttr('disabled');
    }

}

function gestionar_convenio() {
    //* esta funcion crea y edita regitros de convenios
    const id_convenio = $("#tipo_gestion").val();
    const nombre = $("#nombre").val();
    const tipo = $("#tipo").val();
    let pct_consul = $("#pct_consul").val();
    let cost_consul = $("#cost_consul").val();

    // Asignar valores predeterminados si están vacíos
    pct_consul = pct_consul || 0;
    cost_consul = cost_consul || 0;

    // Arreglo para campos faltantes
    const camposFaltantes = [];

    // Validaciones
    if (!nombre) {
        $("#nombre").addClass('is-invalid');
        camposFaltantes.push('nombre');
    }

    if (!tipo) {
        $("#tipo").addClass('is-invalid');
        camposFaltantes.push('tipo');
    }

    // Mostrar alerta si hay campos faltantes
    if (camposFaltantes.length > 0) {
        Swal.fire({
            icon: "error",
            title: `Debes especificar el ${camposFaltantes.join(" y ")} del convenio`,
            showConfirmButton: false,
            timer: 1500,
        });
        return;
    }

    // Si todas las validaciones pasan, mostrar el mensaje de registro
    Swal.fire({
        title: 'Registrando convenio...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });


    $.ajax({
        cache: false,
        url: 'componentes/catalogos/registrar/registrar_convenio.php',
        type: 'POST',
        dataType: 'html',
        data: {
            'id_convenio': id_convenio,
            'nombre': nombre,
            'tipo': tipo,
            'pct_consul': pct_consul,
            'cost_consul': cost_consul,
        },
    }).done(function (resultado) {
        // console.log(resultado);
        if (resultado == "ok") {
            Swal.fire({
                icon: "success",
                title: "Se registro el convenio",
                showConfirmButton: false,
                timer: 1500
            }).then(function () {
                window.location = 'convenios.php';
            });
        }
        else if (resultado == "actualizado") {
            Swal.fire({
                icon: "success",
                title: "Actializado",
                html: "La informaci&oacute;n se actualizo exitosamente",
                showConfirmButton: false,
                timer: 2000
            }).then(function () {
                window.location = 'convenios.php';
            });
        }
        else {
            Swal.fire({
                icon: "error",
                title: "El producto o servicio no se pudo registrar. Por favor, contacte al soporte técnico para recibir asistencia.",
                // title: resultado,
                showConfirmButton: true,
                timer: 1500
            });
        }
    });
}

function ver_catalogo() {
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/cargar/cargar_convenios.php',
        type: 'POST',
        dataType: 'html',
    }).done(function (resultado) {
        $("#vista_convenios").html(resultado);
        $("#modal_convenios").modal("show");
    });
}

function recargar_tabla_convenios(id_convenio, operacion = 1){
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/cargar/cargar_convenios.php',
        type: 'POST',
        dataType: 'html',
        data: {
            'id_convenio': id_convenio,
            'operacion': operacion,
        },
    }).done(function (resultado) {
        $("#tr_conve_" + id_convenio).replaceWith(resultado);
    });
}

function editar_convenio(id_convenio) {

    $.ajax({
        cache: false,
        url: 'componentes/catalogos/cargar/cargar_datos_convenio.php',
        type: 'POST',
        dataType: 'json',
        data: { 'id_convenio': id_convenio }
    }).done(function (resultado) {

        const { id_convenio, nombre, tipo, pct_consul, cost_consul } = resultado[0];

        $("#modal_convenios").modal("hide");

        $("#tipo_gestion").val(id_convenio);
        $("#nombre").val(nombre);
        $("#tipo").val(tipo);

        if (pct_consul > 0) $("#pct_consul").removeAttr('disabled');
        if (cost_consul > 0) $("#cost_consul").removeAttr('disabled');

        $("#pct_consul").val(pct_consul);
        $("#cost_consul").val(cost_consul);

        $("#btn-convenios").html('Actualizar Convenio');
    });
}

function actualizar_estatus_convenio(id_convenio, codigo_estatus) {
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/actualizar/actualizar_estatus_convenio.php',
        type: 'POST',
        dataType: 'html',
        data: { 'id_convenio': id_convenio, 'codigo_estatus': codigo_estatus }
    }).done(function (resultado) {
        if (resultado == "ok") {
            Swal.fire({
                icon: "success",
                title: "Se actualizo estatus del convenio",
                showConfirmButton: false,
                timer: 1500
            }).then(function () {
                ver_catalogo();
            });
        } else {
            Swal.fire({
                icon: "error",
                title: "El estatus del convenio no pudo ser actualizado. Por favor, contacte al soporte técnico para recibir asistencia.",
                // title: resultado,
                showConfirmButton: true,
                timer: 1500
            });
        }
    });
}