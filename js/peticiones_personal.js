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

function gestionar_personal(redireccion, modal_agenda = '', modal_cliente = '') {

    //console.log(redireccion)
    // const no_fisico = $('input:radio[id=no_fisico]:checked').val();
    // const fisico = $('input:radio[id=fisico]:checked').val();
    // const fiscal = no_fisico == 1 ? 1 : 2;

    const tipo_gestion = $('#tipo_gestion').val();
    const nombre_personal = $('#nombre_personal').val();
    const tipo_personal = $('#tipo_personal').val();
    const calle = $('#calle').val();
    const no_exterior = $('#no_exterior').val();
    const no_interior = $('#no_interior').val();
    const codigo_postal = $('#codigo_postal').val();
    const tipo_colonia = $("#colonia_oculta").val();
    const colonia = tipo_colonia == 2 ? $('#colonia_text').val() : $('#colonia').val();
    const estado = $('#estado').val();
    const municipio = $('#municipio').val();
    const pais = $('#pais').val();
    const correo = $('#correo').val();
    const telefono = $('#telefono').val();
    const estatus = 1;

    // Arreglo para almacenar campos faltantes
    const camposFaltantes = [];

    // Validación de tipo de personal
    if (!tipo_personal) {
        $("#tipo_personal").addClass('is-invalid');
        camposFaltantes.push("Tipo de personal");
    }

    // Validación del nombre del personal
    if (!nombre_personal) {
        $("#nombre_personal").addClass('is-invalid');
        camposFaltantes.push("Nombre del personal");
    }

    // Validaciones adicionales
    /*
    if (!calle) {
        $("#calle").addClass('is-invalid');
        camposFaltantes.push("Calle");
    }

    if (!no_exterior) {
        $("#no_exterior").addClass('is-invalid');
        camposFaltantes.push("Número exterior");
    }

    if (!codigo_postal) {
        $("#codigo_postal").addClass('is-invalid');
        camposFaltantes.push("Código Postal");
    }

    if (!colonia) {
        if (tipo_colonia == 2) {
            $("#colonia_text").addClass('is-invalid');
        } else {
            $("#colonia").addClass('is-invalid');
        }
        camposFaltantes.push("Colonia");
    }

    if (!estado) {
        $("#estado").addClass('is-invalid');
        camposFaltantes.push("Estado");
    }

    if (!municipio) {
        $("#municipio").addClass('is-invalid');
        camposFaltantes.push("Municipio");
    }

    if (!pais) {
        $("#pais").addClass('is-invalid');
        camposFaltantes.push("País");
    }
        */

    if (!correo) {
        $("#correo").addClass('is-invalid');
        camposFaltantes.push("Correo");
    }

    if (!telefono) {
        $("#telefono").addClass('is-invalid');
        camposFaltantes.push("Teléfono");
    }

    // Mostrar errores si hay campos faltantes
    if (camposFaltantes.length > 0) {
        Swal.fire({
            icon: "warning",
            title: "Por favor, complete los siguientes campos obligatorios:",
            html: camposFaltantes.join(", "),
            showConfirmButton: true,
        });
        return;
    }

    // Si todas las validaciones pasan, mostrar el mensaje de registro
    Swal.fire({
        title: 'Registrando personal...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });


    $.ajax({
        cache: false,
        url: "componentes/catalogos/registrar_personal.php",
        type: 'POST',
        dataType: 'html',
        data: {
            'nombre_personal': nombre_personal,
            'tipo_personal': tipo_personal,
            'calle': calle,
            'no_exterior': no_exterior,
            'no_interior': no_interior,
            'codigo_postal': codigo_postal,
            'colonia': colonia,
            'estado': estado,
            'municipio': municipio,
            'pais': pais,
            'correo': correo,
            'telefono': telefono,
            'estatus': estatus,
            'id_personal': tipo_gestion
        },
    }).done(function (resultado) {

        if (resultado == "ok") {
            Swal.fire({
                icon: "success",
                title: "Personal Registrado",
                html: "La informaci&oacute;n se registro exitosamente",
                showConfirmButton: false,
                timer: 2000
            }).then(function () {
                if (redireccion) {
                    window.location = redireccion;
                }

                if (modal_agenda && modal_cliente) {
                    $('#' + modal_cliente).modal('hide');
                    $("#" + modal_agenda).modal("show");
                }

            });
        }
        else if (resultado == "actualizado") {
            Swal.fire({
                icon: "success",
                title: "Personal Actializado",
                html: "La informaci&oacute;n se registro exitosamente",
                showConfirmButton: false,
                timer: 2000
            }).then(function () {

                window.location = 'personal.php';
            });
        }
        else if (resultado == "error") {
            Swal.fire({
                icon: "warning",
                title: "Personal No Registrado",
                showConfirmButton: false,
                timer: 2000
            });
        }
        else {
            Swal.fire({
                icon: "warning",
                title: "El Personal Ya Existe",
                showConfirmButton: false,
                timer: 2000
            });
        }
    });
}

function ver_catalogo() {
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/cargar/cargar_personal.php',
        type: 'POST',
        dataType: 'html',
    }).done(function (resultado) {
        $("#vista_personal").html(resultado);
        $("#modal_personal").modal("show");
    });
}
function actualizar_estatus_personal(id_personal, estatus) {
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/actualizar_estatus_personal.php',
        type: 'POST',
        dataType: 'html',
        data: { 'id_personal': id_personal, 'estatus': estatus },
    }).done(function (resultado) {
        Swal.fire({
            icon: 'success',
            title: 'Estatus actualizado!',
            showConfirmButton: false,
            timer: 2000
        }).then(function () {
            recargar_tabla_personal(id_personal, 2)
        });
    });
}

function recargar_tabla_personal(id_personal, tipo = 1) {
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/cargar/cargar_personal.php',
        type: 'POST',
        dataType: 'html',
        data: {
            'id_personal': id_personal,
            'tipo': tipo
        },
    }).done(function (resultado) {
        // console.log(resultado);
        $("#tr_pers_" + id_personal).replaceWith(resultado);
    });
}

function editar_personal(id_personal, nombre_personal, tipo_personal, calle, exterior, interior, codigo_postal, colonia, municipio, estado, pais, correo, telefono) {

    $("#leyenda").html("Modificar datos del personal");
    $("#tipo_gestion").val(id_personal);
    $("#id_personal_input").val(id_personal);

    $("#nombre_personal").val(nombre_personal);
    $("#tipo_personal").val(tipo_personal);
    $("#calle").val(calle);
    $("#no_exterior").val(exterior);
    $("#no_interior").val(interior);
    $("#codigo_postal").val(codigo_postal);
    $("#colonia").val(colonia);
    $("#municipio").val(municipio);
    $("#estado").val(estado);
    $("#pais").val(pais);
    $("#correo").val(correo);
    $("#telefono").val(telefono);


    // props
    $("#no_fisico").prop("checked", false);
    $("#fisico").prop("checked", "checked");

    $("#correo").prop("disabled", false);
    $("#calle").prop("disabled", false);
    $("#no_exterior").prop("disabled", false);
    $("#no_interior").prop("disabled", false);
    $("#codigo_postal").prop("disabled", false);
    $("#municipio").prop("disabled", false);
    $("#estado").prop("disabled", false);
    $("#pais").prop("disabled", false);
    if (colonia.length > 4) {
        var cadena = "";
        cadena = '<div class="input-group-prepend"> <span class="input-group-text"><i class="fas fa-city"></i></span> </div>';
        cadena += '<input type="text" class="form-control" placeholder="Escribe colonia" id="colonia_text" maxlength="100" onfocus="resetear(&quot;colonia_text&quot;)">';
        cadena += '&nbsp; <button type="button" class="btn btn-info" onclick="colonia_select();" title="Capturar colonia"><i class="fas fa-edit"></i></button>';
        $("#dato_colonia").html(cadena);
        $("#colonia_text").val(colonia);
        $("#colonia_oculta").val("2");
    }
    else {
        var cadena = "";
        cadena = '<div class="input-group-prepend"> <span class="input-group-text"><i class="fas fa-city"></i></span> </div>';
        cadena += '<select class="form-control" id="colonia" onfocus="resetear(&quot;colonia&quot;)" disabled> <option value="0">Colonia</option> </select>';
        cadena += '&nbsp; <button type="button" class="btn btn-info" onclick="colonia_text();" title="Capturar colonia"><i class="fas fa-edit"></i></button>';
        $("#dato_colonia").html(cadena);
        $("#colonia_oculta").val("1");
        $.ajax({
            cache: false,
            url: 'componentes/catalogos/buscar_colonia.php',
            type: 'POST',
            dataType: 'html',
            data: { 'codigo': codigo_postal },
        }).done(function (resultado) {
            $("#colonia").html(resultado);
            $("#colonia").prop('disabled', false);
        });
        $("#colonia > option[value='" + colonia + "']").prop("selected", "selected");
    }
    var cadena_pais = "";
    cadena_pais = '<div class="input-group-prepend"> <span class="input-group-text"><i class="fas fa-city"></i></span> </div>';
    cadena_pais += '<select class="form-control" id="pais" onfocus="resetear(&quot;pais&quot;)" disabled><option value="0">Pais</option></select>';
    $("#dato_pais").html(cadena_pais);
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/buscar_estado.php',
        type: 'POST',
        dataType: 'html',
        data: { 'codigo': codigo_postal },
    }).done(function (resultado) {
        $("#estado").html(resultado);
        $("#estado").prop('disabled', false);
        $.ajax({
            cache: false,
            url: 'componentes/catalogos/buscar_municipio.php',
            type: 'POST',
            dataType: 'html',
            data: { 'codigo': codigo_postal },
        }).done(function (resultado) {
            $("#municipio").html(resultado);
            $("#municipio").prop('disabled', false);
            $("#pais").html("<option value='MEX'>MEXICO</option>");
            $("#pais").prop('disabled', false);
        });
    });
    habilitar_view_permisos()
    $("#modal_personal").modal("hide");
}



// PERMISOS------------------------------------------

function guardar_permiso() {

    var tipo_gestion = $("#tipo_gestion_permiso").val();
    var fecha_inicial = $("#fecha_inicial").val();
    var fecha_final = $("#fecha_final").val();
    var motivo_permiso = $("#motivo_permiso").val();
    var id_personal = $("#tipo_gestion").val();

    if (id_personal == '0') {
        $("#id_personal").addClass('is-invalid');
        Swal.fire({
            icon: "error",
            title: "Debes seleccionar un terapeuta",
            showConfirmButton: false,
            timer: 1500
        });

        return false;
    }
    if (fecha_inicial.length == 0) {
        $("#fecha_inicio").addClass('is-invalid');
        Swal.fire({
            icon: "error",
            title: "Debes especificar la fecha inicial del permiso",
            showConfirmButton: false,
            timer: 1500
        });

        return false;
    }
    if (fecha_final.length == 0) {
        $("#fecha_final").addClass('is-invalid');
        Swal.fire({
            icon: "error",
            title: "Debes especificar la fecha final del permiso",
            showConfirmButton: false,
            timer: 1500
        });

        return false;
    }
    if (motivo_permiso.length == 0) {
        $("#motivo_permiso").addClass('is-invalid');
        Swal.fire({
            icon: "error",
            title: "Debes especificar el motivo del perimiso",
            showConfirmButton: false,
            timer: 1500
        });

        return false;
    }

    Swal.fire({
        title: 'Registrando Usuario...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });
    if (tipo_gestion == '0') {

        $.ajax({
            cache: false,
            url: 'componentes/catalogos/registrar_permiso_personal.php',
            type: 'POST',
            dataType: 'html',
            data: { 'id_personal': id_personal, 'fecha_inicial': fecha_inicial, 'fecha_final': fecha_final, 'motivo_permiso': motivo_permiso },
        }).done(function (resultado) {
            console.log(resultado)
            if (resultado == "ok") {
                Swal.fire({
                    icon: "success",
                    title: "Permiso Registrado",
                    html: "La informaci&oacute;n se registro exitosamente",
                    showConfirmButton: false,
                    timer: 2000
                })
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Permiso No Registrado",
                    showConfirmButton: false,
                    timer: 2000
                })
            }
            mostrar_historial_permisos()
        });

    } else {
        $.ajax({
            cache: false,
            url: 'componentes/catalogos/registrar_permiso_personal.php',
            type: 'POST',
            dataType: 'html',
            data: { 'id_permiso': tipo_gestion, 'id_personal': id_personal, 'fecha_inicial': fecha_inicial, 'fecha_final': fecha_final, 'motivo_permiso': motivo_permiso },
        }).done(function (resultado) {
            if (resultado == "ok") {
                Swal.fire({
                    icon: "success",
                    title: "Permiso Actualizado",
                    html: "La informaci&oacute;n se actializo exitosamente",
                    showConfirmButton: false,
                    timer: 2000
                })
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Permiso No Registrado",
                    showConfirmButton: false,
                    timer: 2000
                })
            }
            $("#tipo_gestion_permiso").val('0');
            $("#id_personal_input").val(id_personal);
            $("#fecha_inicial").val('');
            $("#fecha_final").val('');
            $("#motivo_permiso").val('');

            $("#btn-permiso").html('Guardar Permiso');
            mostrar_historial_permisos()
        });
    }
}


function habilitar_view_permisos() {
    html = ' <a class="nav-link" id="custom-tabs-two-profile-tab" data-toggle="pill" href="#custom-tabs-two-profile" role="tab" aria-controls="custom-tabs-two-profile" aria-selected="false"><i class="far fa-id-badge"></i> &nbsp;Permisos</a> ';

    mostrar_historial_permisos()
    $("#permisos_view").html(html);

}

function mostrar_historial_permisos() {
    var id_personal = $("#tipo_gestion").val();

    Swal.fire({
        title: 'Cargando permisos...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    $.ajax({
        cache: false,
        url: 'componentes/catalogos/cargar_permisos.php',
        type: 'POST',
        dataType: 'html',
        data: { 'id_personal': id_personal },
    }).done(function (resultado) {
        $("#historial_permisos").html(resultado);
        Swal.close();
    });
}

function editar_permiso(id_permiso, id_personal, fecha_inicial, fecha_final, motivo) {
    //)
    $("#tipo_gestion_permiso").val(id_permiso);
    $("#id_personal_input").val(id_personal);
    $("#fecha_inicial").val(fecha_inicial);
    $("#fecha_final").val(fecha_final);
    $("#motivo_permiso").val(motivo);

    $("#btn-permiso").html('Editar Permiso');

}


function cancelar_permiso(id_permiso, estatus) {
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/actualizar_estatus_permiso.php',
        type: 'POST',
        dataType: 'html',
        data: { 'id_permiso': id_permiso, 'estatus': estatus },
    }).done(function (resultado) {
        console.log(resultado)
        mostrar_historial_permisos()
        Swal.close();
    });
}
