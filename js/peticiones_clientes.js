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
function buscar_cp() {
    var codigo = $("#codigo_postal").val();
    var rfc = $("#rfc").val();
    if (codigo.length == 5 && rfc != "XEXX010101000") {
        $.ajax({
            cache: false,
            url: 'componentes/catalogos/buscar_colonia.php',
            type: 'POST',
            dataType: 'html',
            data: { 'codigo': codigo },
        }).done(function (resultado) {
            $("#colonia").html(resultado);
            $("#colonia").prop('disabled', false);

            $.ajax({
                cache: false,
                url: 'componentes/catalogos/buscar_estado.php',
                type: 'POST',
                dataType: 'html',
                data: { 'codigo': codigo },
            }).done(function (resultado) {
                $("#estado").html(resultado);
                $("#estado").prop('disabled', false);

                $.ajax({
                    cache: false,
                    url: 'componentes/catalogos/buscar_municipio.php',
                    type: 'POST',
                    dataType: 'html',
                    data: { 'codigo': codigo },
                }).done(function (resultado) {
                    $("#municipio").html(resultado);
                    $("#municipio").prop('disabled', false);

                    $("#pais").html("<option value='MEX'>MEXICO</option>");
                    $("#pais").prop('disabled', false);
                });
            });
        });
    }
    else {
        $("#colonia").html("<option value='0'>Colonia</option>");
        $("#colonia").prop('disabled', 'disabled');
        $("#estado").html("<option value='0'>Estado</option>");
        $("#estado").prop('disabled', 'disabled');
        $("#municipio").html("<option value='0'>Municipio</option>");
        $("#municipio").prop('disabled', 'disabled');
        $("#pais").html("<option value='0'>Pa&iacute;s</option>");
        $("#pais").prop('disabled', 'disabled');
    }
}

function colonia_text() {
    var cadena = "";
    cadena = '<div class="input-group-prepend"> <span class="input-group-text"><i class="fas fa-city"></i></span> </div>';
    cadena += '<input type="text" class="form-control" placeholder="Escribe colonia" id="colonia_text" maxlength="100" onfocus="resetear(&quot;colonia_text&quot;)">';
    cadena += '&nbsp; <button type="button" class="btn btn-info" onclick="colonia_select();" title="Capturar colonia"><i class="fas fa-edit"></i></button>';
    $("#dato_colonia").html(cadena);
    $("#colonia_oculta").val("2");
}

function colonia_select() {
    var cadena = "";
    cadena = '<div class="input-group-prepend"> <span class="input-group-text"><i class="fas fa-city"></i></span> </div>';
    cadena += '<select class="form-control" id="colonia" onfocus="resetear(&quot;colonia&quot;)" disabled> <option value="0">Colonia</option> </select>';
    cadena += '&nbsp; <button type="button" class="btn btn-info" onclick="colonia_text();" title="Capturar colonia"><i class="fas fa-edit"></i></button>';
    $("#dato_colonia").html(cadena);
    $("#colonia_oculta").val("1");
}

function activar_forma() {
    var metodo = $("#metodo_pago").val();

    if (metodo == "PPD") {
        cadena = '<option value="99">[99] POR DEFINIR </option>';
        $("#forma_pago").html(cadena);
    }
    else {
        return $.ajax({
            cache: false,
            url: 'componentes/catalogos/cargar_forma_pago.php',
            type: 'POST',
            dataType: 'html',
        }).done(function (resultado) {
            $("#forma_pago").html(resultado);
        });
    }
}

function recargar_lista_clientes(id_cliente, estatus) {
    //* esta funcion carga la ultima modificacion de la tabla de clientes

    $.ajax({
        cache: false,
        url: 'componentes/catalogos/recargar/recargar_lista_clientes.php',
        type: 'POST',
        dataType: 'html',
        data: { 'id_cliente': id_cliente, 'estatus': estatus }
    }).done(function (resultado) {
        $('#tr_cli_' + id_cliente).replaceWith(resultado);
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
        $("#ocupacion").html(resultado);
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
function gestionar_cliente() {

    var tipo_gestion = $('#tipo_gestion').val();
    var nombre_social = $('#nombre_social').val();
    var estado_civil = $("#estado_civil").val();
    var ocupacion = $("#ocupacion").val();
    var fecha_nacimiento = $("#fecha_nac").val();
    var correo = $('#correo').val();
    var telefono = $('#telefono').val();

    if (nombre_social.length == 0) {
        $("#nombre_social").addClass('is-invalid');
        Swal.fire({
            icon: "error",
            title: "Debes especificar el nombre del cliente/paciente",
            showConfirmButton: false,
            timer: 1500
        });

        return false;
    }

    Swal.fire({
        title: 'Registrando cliente/paciente...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    $.ajax({
        cache: false,
        url: "componentes/catalogos/registrar/registrar_cliente.php",
        type: 'POST',
        dataType: 'html',
        data: {
            'nombre_social': nombre_social,
            'correo': correo,
            'telefono': telefono,
            'id_cliente': tipo_gestion,
            'estado_civil': estado_civil,
            'ocupacion': ocupacion,
            'fecha_nacimiento': fecha_nacimiento,
        },
    }).done(function (resultado) {
        //console.log(resultado)
        if (resultado == "ok") {
            Swal.fire({
                icon: "success",
                title: "Cliente/Paciente Registrado",
                html: "La informaci&oacute;n se registro exitosamente",
                showConfirmButton: false,
                timer: 2000
            }).then(function () {
                window.location = "clientes.php";
            });
            /*
            Swal.fire({
                icon: "success",
                title: "Cliente/Paciente Registrado",
                html: "La informaci&oacute;n se registro exitosamente",
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                if(redireccion){
                    window.location = redireccion ;
                }
                if(modal_agenda && modal_cliente){
                    $('#' + modal_cliente).modal('hide');
                    $("#" + modal_agenda).modal("show");
                }
            });
            */
        }
        else {
            Swal.fire({
                icon: "warning",
                title: "Cliente/Paciente No Registrado",
                html: "La informaci&oacute;n no se logro registrar",
                showConfirmButton: false,
                timer: 2000
            });
        }
    });
}

function ver_catalogo() {
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/cargar/cargar_clientes.php',
        type: 'POST',
        dataType: 'html',
    }).done(function (resultado) {
        $("#vista_clientes").html(resultado);
        $("#modal_clientes").modal("show");
    });
}

function actualizar_estatus_cliente(id_cliente, estatus) {
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/actualizar_estatus_cliente.php',
        type: 'POST',
        dataType: 'html',
        data: { 'id_cliente': id_cliente, 'estatus': estatus },
    }).done(function (resultado) {
        Swal.fire({
            icon: 'success',
            title: 'Estatus actualizado!',
            showConfirmButton: false,
            timer: 2000
        }).then(function () {
            recargar_lista_clientes(id_cliente, estatus)
        });
    });
}

function editar_cliente(id_cliente, nombre_social, correo, telefono, fecha_nacimiento, ocupacion, estado_civil) {
    $("#leyenda").html("Modificar datos del cliente");
    $("#tipo_gestion").val(id_cliente);
    $("#nombre_social").val(nombre_social);
    $("#correo").val(correo);
    $("#telefono").val(telefono);
    $("#estado_civil").val(estado_civil);
    $("#ocupacion").val(ocupacion);
    $("#fecha_nac").val(fecha_nacimiento);


    $("#modal_clientes").modal("hide");
}

function cargar_perfil(id_cliente, nombre_cliente = "") {

    $.ajax({
        cache: false,
        url: 'componentes/catalogos/cargar_perfil_facturacion.php',
        type: 'POST',
        dataType: 'html',
        data: { 'id_cliente': id_cliente }
    }).done(function (resultado) {
        $("#vista_perfil_facturacion").html(resultado);
        $("#perfil_id_cliente").val(id_cliente);
        if (nombre_cliente) {
            $("#perfil_nombre_cliente").val(nombre_cliente);
        } else {
            $.ajax({
                cache: false,
                url: 'componentes/catalogos/buscar_perfil_facturacion.php',
                type: 'POST',
                dataType: 'html',
                data: { 'id_cliente': id_cliente }
            }).done(function (resultado) {
                $("#perfil_id_cliente").val(id_cliente);
                $("#perfil_nombre_cliente").val(resultado);
            });
        }

        // si no esya activo el modal, hay qe activarlo, si ya esta activo, no hara anda
        if ($("#modal_perfiles").is(':hidden')) {

            $("#modal_perfiles").modal("show");
        }
    });

}

function recargar_perfiles(id_cliente, id_perfil, tipo, update = false) {
    //? el tipo es de control, para saber si recargara toda la tabla o solo una tupla
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/cargar_perfil_facturacion.php',
        type: 'POST',
        dataType: 'html',
        data: {
            'id_cliente': id_cliente,
            'tipo': tipo,
            'id_perfil': id_perfil,
        }
    }).done(function (resultado) {
        if(update === false){
            $("#tabla_perfil_facturacion tr:first").after(resultado);
        } else {
            $('#tr_perfil_' + id_cliente + '_'+ id_perfil ).replaceWith(resultado);
        }

    });
}

function agregar_perfil() {

    //* recompilacion de informacion------------------------------------------
    const id_perfil = $("#tipo_gestion").val();
    const id_cliente = $("#perfil_id_cliente").val();
    const nombre = $("#perfil_nombre_cliente").val();
    const rfc = $("#rfc").val();
    const nombre_social = $("#nombre_perfil").val();
    const calle = $("#calle").val();
    const no_interior = $("#no_interior").val();
    const no_exterior = $("#no_exterior").val();
    const codigo_postal = $("#codigo_postal").val();
    const colonia = $("#colonia").val();
    const estado = $("#estado").val();
    const municipio = $("#municipio").val();
    const pais = $("#pais").val();
    const regimen = $("#regimen").val();
    const uso_cfdi = $("#uso_cfdi").val();
    const metodo_pago = $("#metodo_pago").val();
    const forma_pago = $("#forma_pago").val();

    const camposFaltantes = [];

    //* validaciones de campos faltantes

    if (rfc.length != 13) {
        $("#rfc").addClass('is-invalid');
        camposFaltantes.push('RFC');
    }
    if (nombre_social.length == 0) {
        $("#nombre_perfil").addClass('is-invalid');
        camposFaltantes.push('Nombre o Razón Social');
    }
    if (calle.length == 0) {
        $("#calle").addClass('is-invalid');
        camposFaltantes.push('Calle');
    }
    if (no_exterior.length == 0) {
        $("#no_exterior").addClass('is-invalid');
        camposFaltantes.push('No. Exterior');
    }
    if (codigo_postal.length == 0) {
        $("#codigo_postal").addClass('is-invalid');
        camposFaltantes.push('Código Postal');
    }
    if (colonia.length == 0 || colonia == 0) {
        if ($("#tipo_colonia").val() == 2 || $("#extranjero").val() == 2) {
            $("#colonia_text").addClass('is-invalid');
            camposFaltantes.push('Colonia (Texto)');
        } else {
            $("#colonia").addClass('is-invalid');
            camposFaltantes.push('Colonia');
        }
    }
    if (estado == 0 || estado.length == 0) {
        $("#estado").addClass('is-invalid');
        camposFaltantes.push('Estado');
    }
    if (municipio == 0 || municipio.length == 0) {
        $("#municipio").addClass('is-invalid');
        camposFaltantes.push('Municipio');
    }
    if (pais == 0 || pais.length == 0) {
        $("#pais").addClass('is-invalid');
        camposFaltantes.push('País');
    }
    if (regimen == 0) {
        $("#regimen").addClass('is-invalid');
        camposFaltantes.push('Régimen Fiscal');
    }
    if (uso_cfdi == 0) {
        $("#uso_cfdi").addClass('is-invalid');
        camposFaltantes.push('Uso CFDI');
    }
    if (metodo_pago == 0) {
        $("#metodo_pago").addClass('is-invalid');
        camposFaltantes.push('Método de Pago');
    }
    if (forma_pago == 0) {
        $("#forma_pago").addClass('is-invalid');
        camposFaltantes.push('Forma de Pago');
    }

    //* alerta con campos faltantes
    if (camposFaltantes.length > 0) {
        Swal.fire({
            icon: "warning",
            title: "Por favor, complete los siguientes campos obligatorios:",
            html: camposFaltantes.join(", "),
            showConfirmButton: true,
        });
        return; // Detener ejecución
    }

    pantallaCarga('Agregando perfil...');

    $.ajax({
        cache: false,
        url: "componentes/catalogos/registrar_perfil_facturacion.php",
        type: 'POST',
        dataType: 'html',
        data: {
            'id_perfil': id_perfil,
            'id_cliente': id_cliente,
            'rfc': rfc,
            'nombre_social': nombre_social,
            'calle': calle,
            'no_exterior': no_exterior,
            'no_interior': no_interior,
            'codigo_postal': codigo_postal,
            'colonia': colonia,
            'municipio': municipio,
            'estado': estado,
            'pais': pais,
            'regimen_fiscal': regimen,
            'metodo_pago': metodo_pago,
            'forma_pago': forma_pago,
            'uso_cfdi': uso_cfdi,
        },
    }).done(function (resultado) {
        Swal.close();
        if (Number(resultado) > 0) {
            Swal.fire({
                icon: "success",
                title: "Perfil de Facuración Registrado",
                html: "La informaci&oacute;n se registro exitosamente",
                showConfirmButton: false,
                timer: 2000
            }).then(function () {
                limpiar_forulario_perfiles();
                if(id_perfil == 0){
                    recargar_perfiles(id_cliente, Number(resultado), 2);
                } else {
                    recargar_perfiles(id_cliente, id_perfil, 2, true);
                }
            });
        } else {
            Swal.fire({
                icon: "error",
                title: "Error Perfil de Facuración Registrado",
                //html: "La informaci&oacute;n no se registro exitosamente",
                html: resultado,
                showConfirmButton: true,
                //timer: 2000
            }).then(function () {
                //window.location = "clientes.php";
            });
        }
    });
}

function limpiar_forulario_perfiles() {
    $("#rfc").val('');
    $("#nombre_perfil").val('');
    $("#calle").val('');
    $("#no_interior").val('');
    $("#no_exterior").val('');
    $("#codigo_postal").val('');
    $("#colonia").val('');
    $("#estado").val('');
    $("#municipio").val('');
    $("#pais").val('');
    $("#regimen").val('0');
    $("#uso_cfdi").val('0');
    $("#metodo_pago").val('0');
    $("#forma_pago").val('0');
}

function editar_perfil(id_cliente, id_perfil) {

    $(".modal-content").animate({ scrollTop: 0 }, "slow");
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/cargar/datos/perfil_facturacion.php',
        type: 'POST',
        dataType: 'json',
        data: {
            'id_cliente': id_cliente,
            'id_perfil': id_perfil,
        }
    }).done(function (resultado) {
        console.log(resultado);
        const {
                id_cliente, 
                id_perfil,
                rfc,
                nombre_social,
                calle,
                no_exterior,
                no_interior,
                codigo_postal,
                colonia,
                regimen_fiscal,
                uso_cfdi,
                metodo_pago,
                forma_pago
            } = resultado[0];
        $("#perfil_id_cliente").val(id_cliente);
        $("#tipo_gestion").val(id_perfil);
        $("#rfc").val(rfc);
        $("#nombre_perfil").val(nombre_social);
        $("#calle").val(calle);
        $("#no_interior").val(no_interior);
        $("#no_exterior").val(no_exterior);
        $("#codigo_postal").val(codigo_postal);
        buscar_cp();
        $("#colonia").val(colonia);
        $("#regimen").val(regimen_fiscal);
        $("#uso_cfdi").val(uso_cfdi.toUpperCase());
        $("#metodo_pago").val(metodo_pago.toUpperCase());
        activar_forma().done(function(){
            $("#forma_pago").val(forma_pago);
        });
    });
}

function actualizar_estatus_perfil(id_cliente, id_perfil, estatus) {
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/actualizar_estatus_perfil_fact.php',
        type: 'POST',
        dataType: 'html',
        data: { 'id_perfil': id_perfil, 'estatus': estatus },
    }).done(function (resultado) {
        Swal.fire({
            icon: 'success',
            title: 'Estatus actualizado!',
            showConfirmButton: false,
            timer: 2000
        }).then(function () {
            recargar_perfiles(id_cliente, id_perfil, 2, true);
        });
    });
}


function eliminar_perfil(id_perfil, id_cliente) {
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/eliminar_perfil_facturacion.php',
        type: 'POST',
        dataType: 'html',
        data: { 'id_perfil': id_perfil },
    }).done(function (resultado) {
        if (resultado == 'ok') {
            Swal.fire({
                icon: 'success',
                title: 'Perfil Eliminado',
                showConfirmButton: false,
                timer: 2000
            }).then(function () {
                cargar_perfil(id_cliente);
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Ocurrio un Error',
                showConfirmButton: false,
                timer: 2000
            })
        }

    });
}

//* funcion se ejecutara al iniciar la pantalla
$(document).ready(function () {
    cargarOcupaciones();
});

function abrir_modal(modal) {
    if (modal.length != 0) {
        //ocultamos el modal de agendar cita
        $('#' + modal).modal('show');
    }
}

function cerrar_modal(modal) {
    if (modal.length != 0) {
        //ocultamos el modal de agendar cita
        $('#' + modal).modal('hide');
    }
}