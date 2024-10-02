function form_nueva_cita() {
    // cargar terapeutas para seleccion
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/cargar_terapeutas.php',
        type: 'POST',
        dataType: 'html',
    }).done(function (resultado) {
        $("#terapeuta").html(resultado);
        $("#modal_productos").modal("show");
    });

    // cargar cosultorios para seleccion
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/cargar_consultorios.php',
        type: 'POST',
        dataType: 'html',
    }).done(function (resultado) {
        $("#terapeuta").html(resultado);
        $("#modal_productos").modal("show");
    });


    $("#modal_nueva_cita").modal("show");
}


function buscar_cliente(nombre_social) {

    $.ajax({
        cache: false,
        url: 'componentes/catalogos/buscar_clientes.php',
        type: 'POST',
        data: { 'nombre_social': nombre_social.value }
    }).done(function (data) {
        console.log(data)
        $("#id_cliente2").val(data);
    })
}


function gestionar_cita() {

    var id_folio = $("#tipo_gestion").val();

    var id_cliente = $("#id_cliente2").val();
    if (id_cliente == '0') {

        $("#cliente_form").addClass('is-invalid');
        return;
    }

    var fecha_hora_cita = $("#fecha_hora_cita_form").val();
    if (!fecha_hora_cita) {

        $("#fecha_hora_cita_form").addClass('is-invalid');

        return;
    }

    var id_terapeuta = $("#terapeuta_form").val();
    if (!id_terapeuta) {

        $("#terapeuta_form").addClass('is-invalid');

        return;
    }

    var tipo_servicio = $("#tipo_servicio_form").val();
    if (!tipo_servicio) {

        $("#tipo_servicio_form").addClass('is-invalid');

        return;
    }

    var tipo_cita = $("#tipo_cita_form").val();
    if (!tipo_cita) {

        $("#tipo_cita_form").addClass('is-invalid');

        return;
    }

    var id_consultorio = $("#consultorio_form").val();
    if (tipo_servicio == '1') {
        if (!id_consultorio) {

            $("#consultorio_form").addClass('is-invalid');

            return;
        }
    }

    var observaciones = $("#observaciones_form").val();

    Swal.fire({
        title: 'Registrando cita...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }

    }); $.ajax({
        cache: false,

        url: "componentes/catalogos/registrar_cita.php",
        type: 'POST',
        dataType: 'html',
        data: { 'tipo_gestion': id_folio, 'id_cliente': id_cliente, 'id_consultorio': id_consultorio, 'id_terapeuta': id_terapeuta, 'tipo_servicio': tipo_servicio, 'tipo_cita': tipo_cita, 'fecha_hora': fecha_hora_cita, 'observaciones': observaciones },
    }).done(function (resultado) {

        if (resultado == "ok") {
            Swal.fire({
                icon: "success",
                title: "Cita Registrada",
                html: "La informaci&oacute;n se registro exitosamente",
                showConfirmButton: false,
                timer: 2000
            }).then(function () {
                window.location = 'agenda.php';
            });
        }
        else if (resultado == "actualizado") {
            Swal.fire({
                icon: "success",
                title: "Cita Actializada",
                html: "La informaci&oacute;n se registro exitosamente",
                showConfirmButton: false,
                timer: 2000
            }).then(function () {
                window.location = 'agenda.php';
            });
        }
        else if (resultado == "error") {
            Swal.fire({
                icon: "warning",
                title: "Cita No Registrada",
                showConfirmButton: false,
                timer: 2000
            });
        }

        //-------------VALIDACIONES DE EXISTENCIA EN LA AGENDA -------------

        else if (resultado == 1) // validacion para cliente y hora
        {
            Swal.fire({
                icon: "warning",
                title: "El Cliente Ya Tiene Cita En El Horario: " + fecha_hora_cita,
                showConfirmButton: false,
                timer: 2000
            });
        }
        else if (resultado == 2) // validacion para conultorio y hora
        {
            Swal.fire({
                icon: "warning",
                title: "El Consultorio Esta Ocupado En El Horario: " + fecha_hora_cita,
                showConfirmButton: false,
                timer: 2000
            });
        }
        else if (resultado == 3) // validacion para terapeuta y hora
        {
            Swal.fire({
                icon: "warning",
                title: "El Terapeuta Esta Ocupado En El Horario: " + fecha_hora_cita,
                showConfirmButton: false,
                timer: 2000
            });
        }
        else {
            Swal.fire({
                icon: "warning",
                //title: "El Personal Ya Existe",
                title: resultado,
                showConfirmButton: true,
                //timer: 2000
            });
        }
    });

}


function cargar_datos() {

    id_cliente = $("#id_cliente2").val();
    fecha_hora_cita = $("#fecha_hora_cita_form").val();
    movimiento = 1;
    
    $.ajax({
        cache: false,
        url: "componentes/catalogos/cargar_terapeutas.php",
        type: 'POST',
        dataType: 'html',
        data: { 'movimiento': movimiento, 'id_cliente': id_cliente, 'fecha_hora': fecha_hora_cita, },
    }).done(function (resultado) {
        //console.log(resultado)
        if (movimiento == 1) {
            $("#terapeuta_form").html(resultado);
        }
    })


    $.ajax({
        cache: false,

        url: "componentes/catalogos/cargar_consultorios.php",
        type: 'POST',
        dataType: 'html',
        data: { 'fecha_hora': fecha_hora_cita, },
    }).done(function (resultado) {
        //console.log(resultado)
        $("#consultorio_form").html(resultado);
    })
}

function tipo_servicio() {
    tipo = $("#tipo_servicio_form").val();

    if (tipo == '2') {
        $("#consultorio_form").val(0);
        $("#consultorio_form").prop('disabled', true);
    }
    if (tipo == '1') {
        $("#consultorio_form").val();
        $("#consultorio_form").prop('disabled', false);
    }

}

function actualizar_lista_clientes() {
    
    $.ajax({
        cache: false,
        url: "componentes/catalogos/cargar_list_clientes.php",
        type: 'POST',
        dataType: 'html',
    }).done(function (resultado) {
        $("#clientes").html(resultado);
        $("#cliente_form").removeClass('is-invalid');
    })

}

function mostrar_historial_citas() {
    var id_cliente = $("#id_cliente2").val();
    var estatus = $("#estatus").val();
    var terapeuta = $("#terapeutas").val();
    var consultorio = $("#consultorio").val();
    var finicial = $("#fecha_inicial").val();
    var ffinal = $("#fecha_final").val();

    if (finicial.length == 0) {
        $("#fecha_inicial").addClass('is-invalid');
        return false;
    }
    if (ffinal.length == 0) {
        $("#fecha_final").addClass('is-invalid');
        return false;
    }

    Swal.fire({
        title: 'Cargando citas...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    $.ajax({
        cache: false,
        url: 'componentes/citas/historial_citas.php',
        type: 'POST',
        dataType: 'html',
        data: { 'id_cliente': id_cliente, 'estatus': estatus, 'id_terapeuta': terapeuta, 'id_consultorio': consultorio, 'fecha_inicial': finicial, 'fecha_final': ffinal },
    }).done(function (resultado) {
        $("#historial_citas").html(resultado);
        Swal.close();
    });
}


function editar_cita(folio_cita, nombre_cliente, id_consultorio, id_terapeuta, tipo_servicio, tipo_cita, fecha_agenda, observaciones) {

    $("#tipo_gestion").val(folio_cita);
    $("#cliente_form").val(nombre_cliente);
    $("#fecha_hora_cita_form").val(fecha_agenda);
    $("#terapeuta_form").val(id_terapeuta);
    $("#tipo_cita_form").val(tipo_cita);
    $("#tipo_servicio_form").val(tipo_servicio);
    $("#consultorio_form").val(id_consultorio);
    $("#observaciones_form").val(observaciones);

    $("#modal_nueva_cita").modal("show");
}

function cancelar_cita(id_folio, nombre_cliente, nombre_terapeuta, nombre_consultorio) {

    $("#folio_cancelar").html(id_folio);
    $("#cliente_cancelar").html("Cliente: " + nombre_cliente);
    $("#terapeuta_cancelar").html("Terapeuta: " + nombre_terapeuta);
    $("#consultorio_cancelar").html("Consultorio: " + nombre_consultorio);
    $('#modal_cancelacion').modal('show');
}

function enviar_cancelacion() {
    motivo = $("#motivo_cancelacion").val();
    id_folio = $("#folio_cancelar").html();
    if (!motivo) {
        $("#motivo_cancelacion").addClass('is-invalid');
        return;
    }

    Swal.fire({
        title: 'Procesando cancelación...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    $.ajax({
        cache: false,
        url: 'componentes/citas/registrar_cancelacion.php',
        type: 'POST',
        dataType: 'html',
        data: { 'id_folio': id_folio, 'motivo': motivo },
    }).done(function (resultado) {
        Swal.close();
        if (resultado == "ok") {
            Swal.fire({
                icon: "success",
                title: "Cancelaci&oacute;n exitosa",
                showConfirmButton: false,
                timer: 2000
            });
            $("#btn_edit_" + id_folio).prop("disabled", "disabled");
            $("#btn_can_" + id_folio).prop("disabled", "disabled");
            $("#td_ef_" + id_folio).html('<span class="badge badge-secondary" style="width: 100%; color:white;">CANCELADO</span>');
            $("#modal_cancelacion").modal("hide");
        }
        if (resultado == "error") {
            Swal.fire({
                icon: "error",
                title: "No se logro realizar la cancelaci&oacute;n",
                showConfirmButton: false,
                timer: 2000
            });
        }
        if (resultado == "error2") {
            Swal.fire({
                icon: "error",
                title: "No se logro actualizar el estatus de la cita",
                showConfirmButton: false,
                timer: 2000
            });
        }
    });
}



// Peticiones Modales
function abrir_modal(modal1, modal2) {

    if (modal1.length != 0) {
        //ocultamos el modal de agendar cita
        $('#' + modal1).modal('hide');
    }

    if (modal2.length != 0) {
        // abrimos el modal del cliente
        $("#" + modal2).modal("show");
    }

}

function cerrar_modal(modal1, modal2) {
    if (modal1.length != 0) {
        //ocultamos el modal de agendar cita
        $('#' + modal1).modal('hide');
    }

    if (modal2.length != 0) {
        // abrimos el modal del cliente
        $("#" + modal2).modal("show");
    }
}

function disponibilidad_terapeutas() {

    abrir_modal('modal_nueva_cita', 'modal_ver_terapeuta')

    var fecha_hora = $("#fecha_hora_cita_form").val();
    var movimiento = '2';
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/cargar_terapeutas.php',
        type: 'POST',
        dataType: 'html',
        data: {'fecha_hora': fecha_hora, 'movimiento': movimiento},
    }).done(function(resultado){
        $("#disponibilidad_terapeutas").html(resultado)
    });

}
