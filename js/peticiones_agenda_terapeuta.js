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

function realizar_valoracion(id_folio, id_cliente, nombre_cliente, telefono, fecha_nacimiento, ocupacion, estado_civil) {

    edad = calcularEdad(fecha_nacimiento);
    $("#id_cliente_valoracion").val(id_cliente);
    $("#id_folio_cita").val(id_folio);
    $("#nombre_valoracion").val(nombre_cliente);
    $("#telefono_valoracion").val(telefono);
    $("#estado_civil_valoracion").val(estado_civil);
    
    $('#edad_valoracion').val(edad);

    cargarOcupaciones()
    .then(function () {
        $('#ocupacion_valoracion').val(ocupacion);
    });
    cargarEnfermedades();
    cargar_datos_tabla_enfermedades()

    $('#modal_valoracion').modal('show');
}

function enviar_valoracion() {
    var id_cliente = $("#id_cliente_valoracion").val();
    var id_folio = $("#id_folio_cita").val();
    // Validaciones de los campos obligatorios
    var edad = isRequired('edad_valoracion');
    var ocupacion = isRequired('ocupacion_valoracion');
    var telefono = isRequired('telefono_valoracion');
    //var toximanias = isRequired('toximanias_valoracion');
    var motivo_consulta = isRequired('motivo_consulta_valoracion');
    var act_fisica = isRequired('act_fisica_valoracion');
    var farmacos = isRequired('farmacos');
    var escalaDolor = $("#escalaDolor").val();
    if (!escalaDolor || escalaDolor == '0') {
        $("#escaDolMessage").html('Campo obligatorio')
    }


    // Si alguna de las validaciones falla, detener la ejecución
    /*
    if (!edad || !ocupacion || !telefono || !toximanias || !motivo_consulta || !act_fisica || !farmacos || !escalaDolor) {
        alert("Faltan campos obligatorios.");
        return; // Detener la función si hay algún campo inválido
    }
        */
    // Si alguna de las validaciones falla, detener la ejecución
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
        return; // Detener la función si hay algún campo inválido
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

    // Si todas las validaciones son correctas, mostrar pantalla de carga y realizar la petición
    pantallaCarga('Guardando valoracion...');

    $.ajax({
        cache: false,
        url: 'componentes/catalogos/registrar/registrar_valoracion_cita.php',
        type: 'POST',
        dataType: 'html',
        data: {
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
        },
    }).done(function (resultado) {
        // console.log(resultado);
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
            } else if( resultado == 'ex'){
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
    if (!enfermedad|| !tiempo || !toma_medicamento){
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






