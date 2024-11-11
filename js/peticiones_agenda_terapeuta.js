function isRequired(idInput) {
    var variable = $("#" + idInput).val();

    if (!variable || Number(variable) == 0 || variable.length == 0) {
        $("#" + idInput).addClass('is-invalid');
        return false; // Retornar 'false' si falla la validación
    } else {
        $("#" + idInput).removeClass('is-invalid'); // Remover la clase si es válido
        return variable; // Retornar el valor válido
    }
}

function pantallaCarga(texto) {
    Swal.fire({
        title: texto,
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });
}

function calcularEdad(fechaNacimiento) {
    const hoy = new Date(); // Fecha actual
    const fechaNac = new Date(fechaNacimiento); // Fecha de nacimiento convertida a tipo Date

    let edad = hoy.getFullYear() - fechaNac.getFullYear(); // Diferencia de años

    // Ajuste si el cumpleaños de este año aún no ha pasado
    const mes = hoy.getMonth() - fechaNac.getMonth();
    if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
        edad--;
    }

    return edad;
}

function cargarOcupaciones() {
    return $.ajax({
        cache: false,
        url: "componentes/catalogos/cargar/cargar_ocupaciones.php",
        type: 'POST',
        dataType: 'html',
        data: {},
    }).done(function (resultado) {
        //console.log(resultado)
        $("#ocupacion_valoracion").html(resultado);
    });
}

function cargarEnfermedades() {
    let folio = $("#id_folio_cita").val();
    $.ajax({
        cache: false,
        url: "componentes/catalogos/cargar/cargar_enfermedades.php",
        type: 'POST',
        dataType: 'html',
        data: {},
    }).done(function (resultado) {
        //console.log(resultado)
        $("#enfermedades").html(resultado);
    });
}

function cargar_datos_tabla_enfermedades() {

    let id_folio = $("#id_folio_cita").val();

    $.ajax({
        cache: false,
        url: "componentes/catalogos/cargar/cargar_datos_enfermedades.php",
        type: 'POST',
        dataType: 'html',
        data: { 'id_folio_cita': id_folio },
    }).done(function (resultado) {
        //console.log(resultado);
        $("#form_enfermedad").html(resultado);
    });
}

function cargarDatosActualizarCliente(id) {
    return $.ajax({
        cache: false,
        url: 'componentes/catalogos/cargar/cargar_datos_cientes_valoracion.php',
        type: 'POST',
        dataType: 'json',
        data: {
            'id_cliente': id,
        },
    }).done(function (resultado) {

        // console.log(resultado[0])
        $("#estado_civil_valoracion").val(resultado[0].est_civ);
        $("#estado_civil_valoracion").attr('disabled', true);

        $("#fecha_nacimiento").val(resultado[0].fec_nac);
        $("#fecha_nacimiento").attr('disabled', true);

        edad = calcularEdad(resultado[0].fec_nac);
        $('#edad_valoracion').val(edad);

        $('#ocupacion_valoracion').val(resultado[0].ocupacion);
        $('#ocupacion_valoracion').attr('disabled', true);
        
        $('#telefono_valoracion').val(resultado[0].telefono);
        $('#telefono_valoracion').attr('disabled', true);

        $("#boton_confirmar").html('');
    })
}

async function cargarDatosValoracion(id_folio, id_cliente, tipo) {
    try {
        //* Esta funcion trae los datos necearios para las valoraciones subsecuentes y de primera vez
        //* Trae informacion sobre el folio y datos de cliente
        const datos = await $.ajax({
            cache: false,
            url: 'componentes/catalogos/cargar/cargar_valoracion.php',
            type: 'POST',
            dataType: 'json',
            data: {
                'id_cliente': id_cliente,
                'folio': id_folio,
                'tipo_consulta': tipo,
            },
        });
        return datos;
    } catch (error) {
        console.error("Error al cargar los datos de valoración:", error);
        // Puedes retornar un valor por defecto o lanzar el error nuevamente
        return null; // O puedes hacer `throw error;` si deseas manejar el error más arriba
    }
}

function eliminar_enfermedad_valoracion(id_folio, id_enfermedad) {
    pantallaCarga('Eliminando Enfermedad');
    $.ajax({
        cache: false,
        url: "componentes/catalogos/eliminar/eliminar_enfermedad_valoracion.php",
        type: 'POST',
        dataType: 'html',
        data: { 'id_folio': id_folio, 'id_enfermedad': id_enfermedad },
    }).done(function (resultado) {
        //console.log(resultado);
        if (resultado == 'ok') {
            Swal.fire({
                icon: "success",
                title: "Enfermedad eliminada",
                showConfirmButton: false,
                timer: 2000
            });
            cargar_datos_tabla_enfermedades();
            Swal.close();
        } else {
            alert('No se pudo borrar la enfermedad');
        }
    });
}

async function realizar_valoracion_primera_v(id_folio, id_cliente) {

    //* esta funcion inicializa la valoracion primera vez con los tados necesarios

    $("#tipo_consulta").val(1);
    const dataVal = await cargarDatosValoracion(id_folio, id_cliente, 1);
    $("#observaciones").html('<a class="nav-link" id="custom-tabs-one-dg-pv-tab" data-toggle="pill" href="#custom-tabs-one-dg-pv" role="tab" aria-controls="custom-tabs-one-dg-pv" aria-selected="false"> <i class="fas fa-info"></i> &nbsp;Datos Generales</a>');
    //* Esta funcion carga los datos y activa el formulario de 1ra vez
    $("#id_cliente_valoracion").val(dataVal[0].id_cliente);
    $("#id_folio_cita").val(dataVal[0].id_folio);
    $("#nombre_valoracion").val(dataVal[0].nombre_cliente);
    $("#telefono_valoracion").val(dataVal[0].telefono);
    $("#estado_civil_valoracion").val(dataVal[0].est_civ);
    $("#fecha_nacimiento").val(dataVal[0].fec_nac);

    edad = calcularEdad(dataVal[0].fec_nac);
    $('#edad_valoracion').val(edad);

    cargarOcupaciones()
        .then(function () {
            $('#ocupacion_valoracion').val(dataVal[0].ocupacion);
        });
    cargarEnfermedades();
    cargar_datos_tabla_enfermedades()

    $('#modal_valoracion').modal('show');
}

async function realizar_valoracion_subs(id_folio, id_cliente) {

    //* esta funcion inicializa la valoracion subsecuente con los tados necesarios
    $("#tipo_consulta").val(2);
    const dataVal = await cargarDatosValoracion(id_folio, id_cliente, 2);
    // console.log(dataVal);

    //* Esta funcion carga la informacion activa el formulario de las sitas tipo subsecuente
    $("#observaciones").html('<a class="nav-link" id="custom-tabs-one-dg-sb-tab" data-toggle="pill" href="#custom-tabs-one-dg-sb" role="tab" aria-controls="custom-tabs-one-dg-sb" aria-selected="false"> <i class="fas fa-info"></i> &nbsp;Datos Generales</a>');
    $("#id_cliente_valoracion").val(dataVal[0].id_cliente);
    $("#id_folio_cita").val(dataVal[0].id_folio);
    $("#nombre_valoracion").val(dataVal[0].nombre_cliente);
    $("#telefono_valoracion").val(dataVal[0].telefono);
    $("#estado_civil_valoracion").val(dataVal[0].est_civ);
    $("#fecha_nacimiento").val(dataVal[0].fec_nac);
    
    edad = calcularEdad(dataVal[0].fec_nac);
    $('#edad_valoracion').val(edad);

    $("#num_terapia").val(dataVal[0].total_registros);
    $("#cont_int").val(dataVal.continuo == true ? '1' : '2');

    cargarOcupaciones()
        .then(function () {
            $('#ocupacion_valoracion').val(dataVal[0].ocupacion);
        });
    cargarEnfermedades();
    cargar_datos_tabla_enfermedades()

    $('#modal_valoracion').modal('show');

}


function enviar_valoracion() {
    var tipo_consulta = $("#tipo_consulta").val();
    var id_cliente = $("#id_cliente_valoracion").val();
    var id_folio = $("#id_folio_cita").val();
    var edad = isRequired('edad_valoracion');
    var ocupacion = isRequired('ocupacion_valoracion');
    var telefono = isRequired('telefono_valoracion');
    var motivo_consulta = isRequired('motivo_consulta_valoracion');
    var act_fisica = isRequired('act_fisica_valoracion');
    var farmacos = isRequired('farmacos');
    
    var escalaDolor = $(Number(tipo_consulta) == 1 ? "#escalaDolorP" : "#escalaDolorS").val();
    if (!escalaDolor || escalaDolor == '0') {
        $("#escaDolMessage").html('Campo obligatorio');
    }
    if (Number(tipo_consulta) == 1) {
        if (!edad || !ocupacion || !telefono || !motivo_consulta || !act_fisica || !farmacos || !escalaDolor) {
            let camposFaltantes = [];
            if (!edad) camposFaltantes.push("Edad");
            if (!ocupacion) camposFaltantes.push("Ocupación");
            if (!telefono) camposFaltantes.push("Teléfono");
            if (!motivo_consulta) camposFaltantes.push("Motivo de consulta");
            if (!act_fisica) camposFaltantes.push("Actividad física");
            if (!farmacos) camposFaltantes.push("Fármacos");
            if (!escalaDolor) camposFaltantes.push("Escala de dolor");

            Swal.fire({
                icon: "warning",
                title: "Por favor, complete los siguientes campos obligatorios:",
                html: camposFaltantes.join(", "),
                showConfirmButton: true,
            });
            return;
        }
    } else {
        if (!edad || !ocupacion || !telefono || !escalaDolor) {
            let camposFaltantes = [];
            if (!edad) camposFaltantes.push("Edad");
            if (!ocupacion) camposFaltantes.push("Ocupación");
            if (!telefono) camposFaltantes.push("Teléfono");
            if (!escalaDolor) camposFaltantes.push("Escala de dolor");

            Swal.fire({
                icon: "warning",
                title: "Por favor, complete los siguientes campos obligatorios:",
                html: camposFaltantes.join(", "),
                showConfirmButton: true,
            });
            return;
        }
    }

    var toximanias = $("#toximanias_valoracion").val();
    var estado_civil = $("#estado_civil_valoracion").val();
    var ta = $("#tension_art").val();
    var fc = $("#fc").val();
    var fr = $("#fr").val();
    var satO2 = $("#oxigeno").val();
    var temp = $("#temperatura").val();
    var glucosa = $("#glucosa").val();
    var diagnosticoMedico = $("#diagnosticoMedico").val();
    var avance = $("#avance").val();
    var observaciones = $("#observacionesForm").val();

    pantallaCarga('Guardando valoracion...');

    $.ajax({
        cache: false,
        url: 'componentes/catalogos/registrar/registrar_valoracion_cita.php',
        type: 'POST',
        dataType: 'html',
        data: {
            'tipo_consulta': tipo_consulta,
            'id_cliente': id_cliente,
            'id_folio': id_folio,
            'edad': edad,
            'id_ocupacion': ocupacion,
            'telefono': telefono,
            'estado_civil': estado_civil,
            'toximanias': toximanias,
            'motivo_consulta': motivo_consulta,
            'act_fisica': act_fisica,
            'ta': ta,
            'fc': fc,
            'fr': fr,
            'satO2': satO2,
            'temp': temp,
            'glucosa': glucosa,
            'farmacos': farmacos,
            'diagnostico_medico': diagnosticoMedico,
            'escalaDolor': escalaDolor,
            'avance': avance,
            'observaciones': observaciones,
        },
    }).done(function (resultado) {
        console.log(resultado);
        if (resultado == 'ok') {
            Swal.fire({
                icon: "success",
                title: "Valoración Enviada",
                showConfirmButton: false,
                timer: 2000
            }).then(function () {
                window.location = 'agenda.php';
            });
        }
        $("#modal_valoracion").modal("hide");
        Swal.close();
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.error("Error: " + textStatus, errorThrown);
        Swal.fire({
            icon: "error",
            title: "Error al enviar la valoración",
            text: "Ocurrió un problema al intentar guardar los datos. Por favor, inténtelo nuevamente.",
            showConfirmButton: true,
        });
    });
}


function gestionar_ocupacion(modal_show = '', modal_hide = '') {
    var nombre_ocupacion = isRequired('nombre_ocupacion');
    if (nombre_ocupacion) {
        pantallaCarga('Registrando Ocupación...');
        $.ajax({
            cache: false,
            url: "componentes/catalogos/registrar/registrar_ocupacion.php",
            type: 'POST',
            dataType: 'html',
            data: { 'nombre_ocupacion': nombre_ocupacion },
        }).done(function (resultado) {
            //console.log(resultado)
            if (resultado == "ok") {
                Swal.fire({
                    icon: "success",
                    title: "Ocupación Registrada",
                    html: "La informaci&oacute;n se registro exitosamente",
                    showConfirmButton: false,
                    timer: 2000
                }).then(function () {

                    if (modal_show && modal_hide) {

                        cargarOcupaciones();

                        $('#' + modal_hide).modal('hide');
                        $("#" + modal_show).modal("show");

                    }
                });
            } else if (resultado == 'ex') {
                Swal.fire({
                    icon: "warning",
                    title: "La Ocupación Ya Existe",
                    showConfirmButton: false,
                    timer: 2000
                });
            }
            else {
                Swal.fire({
                    icon: "warning",
                    title: "Ocupación No Registrado",
                    html: "La informaci&oacute;n no se logro registrar",
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    }
}

function gestionar_enfermedad(modal_show = '', modal_hide = '') {
    var nombre_enfermedad = isRequired('nombre_enfermedad');
    if (nombre_enfermedad) {
        pantallaCarga('Registrando Enfermedad...');
        $.ajax({
            cache: false,
            url: "componentes/catalogos/registrar/registrar_enfermedad.php",
            type: 'POST',
            dataType: 'html',
            data: { 'nombre_enfermedad': nombre_enfermedad },
        }).done(function (resultado) {
            //console.log(resultado)
            if (resultado == "ok") {
                Swal.fire({
                    icon: "success",
                    title: "La Enfermedad Registrada",
                    html: "La informaci&oacute;n se registro exitosamente",
                    showConfirmButton: false,
                    timer: 2000
                }).then(function () {
                    if (modal_show && modal_hide) {
                        cargarEnfermedades();
                        $('#' + modal_hide).modal('hide');
                        $("#" + modal_show).modal("show");
                    }
                });
            } else if (resultado == 'ex') {
                Swal.fire({
                    icon: "warning",
                    title: "Enfermedad Ya Existe",
                    showConfirmButton: false,
                    timer: 2000
                });
            } else {
                Swal.fire({
                    icon: "warning",
                    title: "Enfermedad No Registrado",
                    html: "La informaci&oacute;n no se logro registrar",
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    }
}

function agregarEnfermedadValoracion() {

    let id_folio = $("#id_folio_cita").val();
    let enfermedad = isRequired('enfermedades');
    let tiempo = isRequired('tiempo_enfermedad');
    let toma_medicamento = isRequired('toma_medicamento');
    if (!enfermedad || !tiempo || !toma_medicamento) {
        Swal.fire({
            icon: "warning",
            title: "Por favor, complete los campos obligatorios",
            showConfirmButton: true,
        });
        return;
    }

    $.ajax({
        cache: false,
        url: "componentes/catalogos/registrar/registrar_datos_enfermedades.php",
        type: 'POST',
        dataType: 'html',
        data: { 'id_folio_cita': id_folio, 'id_enfermedad': enfermedad, 'tiempo': tiempo, 'toma_medicamento': toma_medicamento, },
    }).done(function (resultado) {
        //console.log(resultado);
        if (resultado == 'ok') {
            cargar_datos_tabla_enfermedades();
        } else {
            Swal.fire({
                icon: "warning",
                title: 'Esta enfermedad ya la has agregado',
                showConfirmButton: false,
                timer: 2000
            });
        }
    });

}

function habilitarParaModificar(id_input) {
    $("#" + id_input).removeAttr('disabled');

    $("#boton_confirmar").html('<button type="button" class="btn btn-success" onclick="modificarDatos()">Modificar Datos</button>')
}

function modificarDatos() {

    var id_cliente = $("#id_cliente_valoracion").val();
    var estado_civil = $("#estado_civil_valoracion").val();
    var fecha_nacimiento = $("#fecha_nacimiento").val();
    var telefono = $("#telefono_valoracion").val();
    var ocupacion = $("#ocupacion_valoracion").val();

    $.ajax({
        cache: false,
        url: 'componentes/catalogos/actualizar/actualizar_datos_cliente_valoracion.php',
        type: 'POST',
        dataType: 'html',
        data: {
            'id_cliente': id_cliente,
            'estado_civil': estado_civil,
            'fecha_nacimiento': fecha_nacimiento,
            'ocupacion': ocupacion,
            'telefono': telefono,
        },
    }).done(function (resultado) {
        //console.log(resultado)
        if (resultado == 'ok') {
            Swal.fire({
                icon: "success",
                title: "Datos de Cliente Actualizados",
                showConfirmButton: false,
                timer: 2000
            })
        }
    }).then(function () {
        cargarDatosActualizarCliente(id_cliente).then(function () {
            Swal.close();
        });
    });

}



