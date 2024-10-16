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


function pantallaCarga( texto ){
    Swal.fire({
        title: texto,
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });
}

function cargarOcupaciones() {
    $.ajax({
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

function realizar_valoracion(id_folio, id_cliente,  nombre_cliente, telefono) {

    $("#id_cliente_valoracion").val(id_cliente);
    $("#id_folio_cita").val(id_folio);
    $("#nombre_valoracion").val(nombre_cliente);
    $("#telefono_valoracion").val(telefono);

    cargarOcupaciones();

    $('#modal_valoracion').modal('show');
}

function enviar_valoracion() {
    var id_cliente = $("#id_cliente_valoracion").val();
    var id_folio = $("#id_folio_cita").val();

    var masculino = $("#masculino_valoracion").is(':checked');
    var femenino = $("#femenino_valoracion").is(':checked');

    var genero = '';
    if (masculino && !femenino) {
        genero = 'M';
    } else {
        genero = 'F';
    }

    // Validaciones de los campos obligatorios
    var edad = isRequired('edad_valoracion');
    var ocupacion = isRequired('ocupacion_valoracion');
    var telefono = isRequired('telefono_valoracion');
    var toximanias = isRequired('toximanias_valoracion');
    var motivo_consulta = isRequired('motivo_consulta_valoracion');
    var act_fisica = isRequired('act_fisica_valoracion');
    var farmacos = isRequired('farmacos');
    var escalaDolor = isRequired('escalaDolor');

    // Si alguna de las validaciones falla, detener la ejecución
    if (!edad || !ocupacion || !telefono || !toximanias || !motivo_consulta || !act_fisica || !farmacos || !escalaDolor) {
        alert("Faltan campos obligatorios.");
        return; // Detener la función si hay algún campo inválido
    }

    var domicilio = $("#domicilio_valoracion").val();
    var estado_civil = $("#estado_civil_valoracion").val();
    var ta = $("#tension_art").val();
    var fc = $("#fc").val();
    var fr = $("#fr").val();
    var satO2 = $("#satO2").val();
    var temp = $("#temp").val();
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
            'genero': genero,
            'edad': edad,
            'id_ocupacion': ocupacion,
            'domicilio': domicilio,
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
    });
}



function gestionar_ocupacion( modal_show = '', modal_hide = '' )
{
    pantallaCarga('Registrando Ocupación...');


    var nombre_ocupacion = isRequired('nombre_ocupacion');

    $.ajax({
        cache: false,
        url : "componentes/catalogos/registrar/registrar_ocupacion.php",
        type : 'POST',
        dataType : 'html',
        data : { 'nombre_ocupacion': nombre_ocupacion},
    }).done(function(resultado){
        console.log(resultado)
        if(resultado == "ok")
        {
            Swal.fire({
                icon: "success",
                title: "Consultorio Registrado",
                html: "La informaci&oacute;n se registro exitosamente",
                showConfirmButton: false,
                timer: 2000
            }).then(function() {

                if(modal_show && modal_hide){

                    cargarOcupaciones();

                    $('#' + modal_hide).modal('hide');
                    $("#" + modal_show).modal("show");

                }
            });
        }
        else
        {
            Swal.fire({
                icon: "warning",
                title: "Consultorio No Registrado",
                html: "La informaci&oacute;n no se logro registrar",
                showConfirmButton: false,
                timer: 2000
            });
        }
    });
}



