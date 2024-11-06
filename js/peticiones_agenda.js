$(document).ready(function () {

    id_terapeuta = $("#id_personal").val();

    if (Number(id_terapeuta) == 0) {
        $("#btn-nueva-cita").html('<button type="button" class="btn btn-danger" onclick="form_nueva_cita();"><i class="fas fa-plus"></i> Nueva cita</button>');
    }
});

function limpiar_form() {
    $("#folio_gestion").val(0);
    $("#id_cliente2").val(0);
    $("#clientes").empty();
    $("#fecha_cita_form").val('');
    $("#hora_cita_form").val('');
    $("#terapeuta_form").val('');
    $("#tipo_servicio_form").val('');
    $("#tipo_cita_form").val('');
    $("#consultorio_form").val('');
    $("#observaciones_form").val('');    
}

function form_nueva_cita() {
    limpiar_form();
    $("#modal_nueva_cita").modal("show");
}

function gestionar_cita() {

    var id_folio = $("#folio_gestion").val();

    var id_cliente = $("#id_cliente2").val();
    if (id_cliente == '0') {

        $("#cliente_form").addClass('is-invalid');
        return;
    }

    var fecha_cita = $("#fecha_cita_form").val();
    if (!fecha_cita) {

        $("#fecha_hora_cita_form").addClass('is-invalid');

        return;
    }
    var hora_cita = $("#hora_cita_form").val();
    if (!hora_cita) {

        $("#hora_cita_form").addClass('is-invalid');

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

    });
    $.ajax({
        cache: false,
        url: "componentes/catalogos/registrar_cita.php",
        type: 'POST',
        dataType: 'html',
        data: {
            'tipo_gestion': id_folio,
            'id_cliente': id_cliente,
            'id_consultorio': id_consultorio,
            'id_terapeuta': id_terapeuta,
            'tipo_servicio': tipo_servicio,
            'tipo_cita': tipo_cita,
            'fecha_cita': fecha_cita,
            'hora_cita': hora_cita,
            'observaciones': observaciones
        },
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
                title: "Error interno, notifica a Soporte",
                //title: resultado,
                showConfirmButton: true,
                //timer: 2000
            });
        }
    });

}


function cargar_datos() {
    return new Promise((resolve, reject) => {
        let id_cliente = $("#id_cliente2").val();
        let fecha_cita = $("#fecha_cita_form").val();

        // Si la fecha no está definida, inicializarla con la fecha actual en formato correcto
        if (!fecha_cita) {
            fecha_cita = new Date().toISOString().split('T')[0];  // Formato YYYY-MM-DD
        }

        let movimiento = 1;

        $.ajax({
            cache: false,
            url: "componentes/catalogos/cargar_terapeutas.php",
            type: 'POST',
            dataType: 'html',
            data: {
                'movimiento': movimiento,
                'id_cliente': id_cliente,
                'fecha_hora': fecha_cita,
            },
        }).done(function (resultado) {
            // Si el movimiento es correcto, rellenar el select y resolver la promesa
            if (movimiento == 1) {
                $("#terapeuta_form").html(resultado);
                resolve();  // Promesa resuelta correctamente
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            // En caso de error, rechazar la promesa con el error
            console.error("Error al cargar los terapeutas: ", textStatus, errorThrown);
            reject(textStatus);  // Rechazar la promesa en caso de error
        });
    });
}

function cargar_horarios_disponibles() {

    //console.log(id_terapeuta)
    id_terapeuta = $("#terapeuta_form").val();
    //console.log(id_terapeuta)
    fecha_cita = $("#fecha_cita_form").val();

    folio_gestion = $("#folio_gestion").val();
    hora_gestion = $("#hora_gestion").val();

    $.ajax({
        cache: false,

        url: "componentes/catalogos/cargar_horarios_disponibles.php",
        type: 'POST',
        dataType: 'html',
        data: { 'fecha_hora': fecha_cita, 'id_terapeuta': id_terapeuta, 'folio_gestion': folio_gestion, 'hora_gestion': hora_gestion },
    }).done(function (resultado) {
        //console.log(resultado)
        $("#hora_cita_form").html(resultado);
    })
}
function cargar_consultorios_disponibles() {
    return new Promise(function (resolve, reject) {
        fecha_cita = $("#fecha_cita_form").val();
        hora_cita = $("#hora_cita_form").val();

        $.ajax({
            cache: false,
            url: "componentes/catalogos/cargar_consultorios.php",
            type: 'POST',
            dataType: 'html',
            data: { 'fecha_cita': fecha_cita, 'hora_cita': hora_cita },
        }).done(function (resultado) {
            //console.log(resultado)
            $("#consultorio_form").html(resultado);
            resolve();
        }).fail(function (jqXHR, textStatus, errorThrown) {
            // En caso de error, rechazar la promesa con el error
            console.error("Error al cargar los terapeutas: ", textStatus, errorThrown);
            reject(textStatus);  // Rechazar la promesa en caso de error
        })
    });
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
        //console.log(resultado)
        $("#clientes").html(resultado);
        $("#cliente_form").removeClass('is-invalid');
    })

}

function buscar_cliente(nombre_social) {

    $.ajax({
        cache: false,
        url: 'componentes/catalogos/buscar_clientes.php',
        type: 'POST',
        dataType: 'json',
        data: { 'nombre_social': nombre_social.value }
    }).done(function (data) {
        //console.log(Number(data.total_registros));
        $("#id_cliente2").val(data.id_cliente);

        if(Number(data.total_registros) > 0){
            var tipo = '1'
        } else {
            var tipo = '2' 
        }

        $("#tipo_cita_form").val(tipo);

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


function editar_cita(folio_cita) {

    $.ajax({
        cache: false,
        url: 'componentes/catalogos/cargar/cargar_datos_cita.php',
        type: 'POST',
        dataType: 'json',
        data: { 'id_folio': folio_cita },
    }).done(function (citaData) {
        //console.log(citaData);
        const [fecha, hora] = citaData[0].fecha_agenda.split(' ');

        $("#fecha_cita_form").val(fecha);
        $("#hora_gestion").val(hora);
        $("#folio_gestion").val(folio_cita);
        $("#id_cliente2").val(citaData[0].id_cliente);
        $("#cliente_form").val(citaData[0].nombre_cliente);

        // Cargar datos del select antes de asignar el terapeuta
        cargar_datos().then(function () {
            $("#terapeuta_form").val(citaData[0].id_terapeuta);
        }).catch(function (error) {
            console.error("Ocurrió un error al cargar los terapeutas: ", error);
        });
        cargar_horarios_disponibles();

        $("#hora_cita").val(hora);

        $("#tipo_gestion").val(citaData[0].id_folio);
        $("#tipo_cita_form").val(citaData[0].tipo_cita);
        $("#tipo_servicio_form").val(citaData[0].tipo_servicio);

        cargar_consultorios_disponibles().then(function () {
            $("#consultorio_form").val(citaData[0].id_consultorio);
        }).catch(function () {
            console.error("Ocurrió un error al cargar los consultorios: ", error);
        });

        $("#observaciones_form").val(citaData[0].observaciones);
        $("#fecha_hora_form").val(hora);

        $("#modal_nueva_cita").modal("show");
    });
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


function disponibilidad_terapeutas() {

    abrir_modal('modal_nueva_cita', 'modal_ver_terapeuta')

    var fecha_hora = $("#fecha_cita_form").val();
    var movimiento = '2';
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/cargar_terapeutas.php',
        type: 'POST',
        dataType: 'html',
        data: { 'fecha_hora': fecha_hora, 'movimiento': movimiento },
    }).done(function (resultado) {
        $("#disponibilidad_terapeutas").html(resultado)
    });

}


function enfermedades_form(){

    diabetes = $("#diabetes").prop('checked');
    hta = $("#hta").prop('checked');
    cancer = $("#cancer").prop('checked');
    enfermedades_reumaticas = $("#enfermedades_reumaticas").prop('checked');
    cardiopatias = $("#cardiopatias").prop('checked');
    cirugias = $("#cirugias").prop('checked');
    alergias = $("#alergias").prop('checked');
    transfusiones = $("#transfusiones").prop('checked');
    otros = $("#otros").prop('checked');

    if(diabetes){
        
    }

}

function ver_pdf(id_folio, tipo_cita)
{
    let ruta;
    
    if (tipo_cita == 2){
        ruta = "componentes/formatos_pdf/ver_pdf_valoracion_pv.php?id_folio=" + id_folio;
    } else {
        ruta = "componentes/formatos_pdf/ver_pdf_valoracion_sb.php?id_folio=" + id_folio;
    }
    $("#ruta_pdf").prop("src", ruta);
    $("#pdf_ffactura").html(id_folio);
    $("#ver_pdf_factura").modal("show");
}