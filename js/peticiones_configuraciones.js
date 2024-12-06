function cerrar_sesion(id_usuario) {
    $.ajax({
        cache: false,
        url: 'componentes/login/cerrar_sesion.php',
        type: 'POST',
        dataType: 'html',
        data: { 'id_usuario': id_usuario },
    }).done(function (resultado) {
        if (resultado == "sesion_close") {
            Swal.fire({
                icon: 'warning',
                title: 'Sesión finalizada!',
                showConfirmButton: false,
                timer: 2000
            }).then(function () {
                window.location = 'index.php';
            });
        }
    });
}

function ver_seccion(pagina) {
    Swal.fire({
        title: 'Cargando vista...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    $.ajax({
        cache: false,
        url: "componentes/configuraciones/" + pagina,
        type: 'POST',
        dataType: 'html',
    }).done(function (resultado) {
        Swal.close();
        $("#mostrar_seccion").html(resultado);
    });
}

function gestionar_emisor() {
    var bandera = 1;
    var tipo_colonia = $("#colonia_oculta").val();
    var rfc = $('#rfc').val();
    var nombre_social = $('#nombre_social').val();
    var nombre_comercial = $('#nombre_comercial').val();
    var calle = $('#calle').val();
    var no_exterior = $('#no_exterior').val();
    var no_interior = $('#no_interior').val();
    var codigo_postal = $('#codigo_postal').val();

    var hora_entrada = $('#hora_entrada').val();
    var hora_salida = $('#hora_salida').val();
    var rango_citas = $('#rango_citas').val();

    var hora_entrada_sabado = $('#hora_entrada_sabado').val();
    var hora_salida_sabado = $('#hora_salida_sabado').val();

    var hora_comida_inicio = $('#hora_comida_inicio').val();
    var hora_comida_fin = $('#hora_comida_fin').val();

    if (tipo_colonia == 1) {
        var colonia = $('#colonia').val();
    }
    else {
        var colonia = $('#colonia_text').val();
    }
    var estado = $('#estado').val();
    var municipio = $('#municipio').val();
    var pais = $('#pais').val();
    var regimen = $('#regimen').val();
    var correo = $('#correo').val();
    var telefono = $('#telefono').val();
    var sitio_web = $('#sitio_web').val();

    if (rfc.length == 0 || rfc.length < 12 || rfc == "") {
        $("#rfc").addClass('is-invalid');
        bandera = 2;
    }
    if (nombre_social.length == 0) {
        $("#nombre_social").addClass('is-invalid');
        bandera = 2;
    }
    if (nombre_comercial.length == 0) {
        $("#nombre_comercial").addClass('is-invalid');
        bandera = 2;
    }
    if (calle.length == 0) {
        $("#calle").addClass('is-invalid');
        bandera = 2;
    }
    if (no_exterior.length == 0) {
        $("#no_exterior").addClass('is-invalid');
        bandera = 2;
    }
    if (codigo_postal.length == 0) {
        $("#codigo_postal").addClass('is-invalid');
        bandera = 2;
    }
    if (colonia.length == 0 || colonia == 0) {
        if (tipo_colonia == 1) {
            $("#colonia").addClass('is-invalid');
        }
        else {
            $("#colonia_text").addClass('is-invalid');
        }
        bandera = 2;
    }
    if (estado == 0) {
        $("#estado").addClass('is-invalid');
        bandera = 2;
    }
    if (municipio == 0) {
        $("#municipio").addClass('is-invalid');
        bandera = 2;
    }
    if (pais == 0) {
        $("#pais").addClass('is-invalid');
        bandera = 2;
    }
    if (regimen == 0) {
        $("#regimen").addClass('is-invalid');
        bandera = 2;
    }

    if (Number(rango_citas) < 0) {
        $("#rango_citas").addClass('is-invalid');
        bandera = 2;
    }

    if (bandera == 1) {
        Swal.fire({
            title: 'Actualizando datos...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            }
        });

        $.ajax({
            cache: false,
            url: "componentes/configuraciones/actualizar_emisor.php",
            type: 'POST',
            dataType: 'html',
            data: {
                'rfc': rfc,
                'nombre_social': nombre_social,
                'nombre_comercial': nombre_comercial,
                'calle': calle,
                'no_exterior': no_exterior,
                'no_interior': no_interior,
                'codigo_postal': codigo_postal,
                'colonia': colonia,
                'estado': estado,
                'municipio': municipio,
                'pais': pais,
                'regimen': regimen,
                'correo': correo,
                'telefono': telefono,
                'sitio_web': sitio_web,
                'hora_entrada': hora_entrada,
                'hora_salida': hora_salida,
                'rango_citas': rango_citas,
                'hora_entrada_sabado': hora_entrada_sabado,
                'hora_salida_sabado': hora_salida_sabado,
                'hora_comida_inicio': hora_comida_inicio,
                'hora_comida_fin': hora_comida_fin,
            },
        }).done(function (resultado) {
            //console.log(resultado)
            Swal.fire({
                icon: "success",
                title: "¡Emisor Actualizado!",
                html: "La información se actualizó correctamente.<br><br><strong>Nota:</strong> Para garantizar que los cambios se reflejen adecuadamente, por favor cierra sesión y vuelve a ingresar.",
                confirmButtonText: "Entendido"
            }).then(function () {
                // Redirige al usuario después de confirmar
                cerrar_sesion();
            });
        });
    }
    else {
        $("html, body").animate({ scrollTop: 0 }, 600);
    }
}

function buscar_cp() {
    var codigo = $("#codigo_postal").val();
    if (codigo.length == 5) {
        $.ajax({
            cache: false,
            url: 'componentes/configuraciones/buscar_colonia.php',
            type: 'POST',
            dataType: 'html',
            data: { 'codigo': codigo },
        }).done(function (resultado) {
            $("#colonia").html(resultado);
            $("#colonia").prop('disabled', false);

            $.ajax({
                cache: false,
                url: 'componentes/configuraciones/buscar_estado.php',
                type: 'POST',
                dataType: 'html',
                data: { 'codigo': codigo },
            }).done(function (resultado) {
                $("#estado").html(resultado);
                $("#estado").prop('disabled', false);

                $.ajax({
                    cache: false,
                    url: 'componentes/configuraciones/buscar_municipio.php',
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
    cadena += '<select class="form-control" id="colonia" onfocus="resetear(&quot;colonia&quot;)"> <option value="0">Colonia</option> </select>';
    cadena += '&nbsp; <button type="button" class="btn btn-info" onclick="colonia_text();" title="Capturar colonia"><i class="fas fa-edit"></i></button>';
    $("#dato_colonia").html(cadena);
    $("#colonia_oculta").val("1");
}

function cargar_logo() {
    var bandera = 1;
    var logo = document.querySelector("#logo");
    var tipo = $('#tipo').val();

    if (tipo == 0) {
        $("#tipo").addClass('is-invalid');
        bandera = 2;
    }

    if (bandera == 1) {
        Swal.fire({
            title: 'Cargando informaci&oacute;n...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            }
        });

        let formData = new FormData();
        formData.append('tipo', tipo);
        formData.append('logo', logo.files[0]); // En la posición 0; es decir, el primer elemento
        fetch("componentes/configuraciones/guardar_logo.php", {
            method: 'POST',
            body: formData,
        })
            .then(resultado => resultado.text())
            .then(function (resultado) {
                Swal.close();
                if (resultado == 1) {
                    Swal.fire({
                        icon: "success",
                        title: "Registro actualizado",
                        html: "Informaci&oacute;n cargada exitosamente",
                        showConfirmButton: false,
                        timer: 1500
                    });
                    location.reload();
                }
                if (resultado == 2) {
                    Swal.fire({
                        icon: "warning",
                        title: "Registro incompleto",
                        html: "Se actualiz&oacute; la informaci&oacute;n pero no se logro subir el logotipo",
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
                if (resultado == 3) {
                    Swal.fire({
                        icon: "warning",
                        title: "Peso no permitido",
                        html: "El peso de la imagen debe ser menos de 100 kb",
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
                logo.value = null;
            });
    }
}

function cargar_marca() {
    var bandera = 1;
    var marca = document.querySelector("#marca");
    var tipo = $('#tipo').val();

    if (tipo == 0) {
        $("#tipo").addClass('is-invalid');
        bandera = 2;
    }

    if (bandera == 1) {
        Swal.fire({
            title: 'Cargando informaci&oacute;n...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            }
        });

        let formData = new FormData();
        formData.append('tipo', tipo);
        formData.append('marca', marca.files[0]); // En la posición 0; es decir, el primer elemento
        fetch("componentes/configuraciones/guardar_marca.php", {
            method: 'POST',
            body: formData,
        })
            .then(resultado => resultado.text())
            .then(function (resultado) {
                Swal.close();
                if (resultado == 1) {
                    Swal.fire({
                        icon: "success",
                        title: "Registro actualizado",
                        html: "Informaci&oacute;n cargada exitosamente",
                        showConfirmButton: false,
                        timer: 1500
                    });
                    location.reload();
                }
                if (resultado == 2) {
                    Swal.fire({
                        icon: "warning",
                        title: "Registro incompleto",
                        html: "Se actualiz&oacute; la informaci&oacute;n pero no se logro subir el logotipo",
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
                if (resultado == 3) {
                    Swal.fire({
                        icon: "warning",
                        title: "Peso no permitido",
                        html: "El peso de la imagen debe ser menos de 100 kb",
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
                logo.value = null;
            });
    }
}

function eliminar_lm(tipo) {
    $.ajax({
        cache: false,
        url: 'componentes/configuraciones/eliminar_lm.php',
        type: 'POST',
        dataType: 'html',
        data: { 'tipo': tipo },
    }).done(function (resultado) {
        $("#mostrar_lm").html(resultado);
    });
}

function cargar_sello() {
    var bandera = 1;
    var cer = document.querySelector("#certificado");
    var key = document.querySelector("#key");
    var pass = $('#password').val();
    var fecha = $('#fecha').val();
    var cer2 = $('#certificado').val();
    var key2 = $('#key').val();

    if (cer2.length == 0) {
        $("#certificado").addClass('is-invalid');
        bandera = 2;
    }
    if (key2.length == 0) {
        $("#key").addClass('is-invalid');
        bandera = 2;
    }
    if (pass.length == 0) {
        $("#password").addClass('is-invalid');
        bandera = 2;
    }
    if (fecha.length == 0) {
        $("#fecha").addClass('is-invalid');
        bandera = 2;
    }

    if (bandera == 1) {
        Swal.fire({
            title: 'Cargando informaci&oacute;n...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            }
        });

        let formData = new FormData();
        formData.append('pass', pass);
        formData.append('fecha', fecha);
        formData.append('cer', cer.files[0]); // En la posición 0; es decir, el primer elemento
        formData.append('key', key.files[0]); // En la posición 0; es decir, el primer elemento
        fetch("componentes/configuraciones/guardar_sello.php", {
            method: 'POST',
            body: formData,
        })
            .then(resultado => resultado.text())
            .then(function (resultado) {
                Swal.close();
                if (resultado == 4) {
                    Swal.fire({
                        icon: "success",
                        title: "Registro actualizado",
                        html: "CSD cargado satisfactoriamente",
                        showConfirmButton: false,
                        timer: 1500
                    });
                    location.reload();
                }
                if (resultado == 3) {
                    Swal.fire({
                        icon: "warning",
                        title: "Registro incompleto",
                        html: "Se cargo el CSD pero no se logr&oacute; actualizar la base de datos",
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
                if (resultado == 2) {
                    Swal.fire({
                        icon: "warning",
                        title: "Registro no realizado",
                        html: "No se logr&oacute; cargar el archivo KEY",
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
                if (resultado == 1) {
                    Swal.fire({
                        icon: "error",
                        title: "Registro no realizado",
                        html: "No se logr&oacute; cargar el archivo CER",
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
                cer.value = null;
                key.value = null;
            });
    }
}

function gestionar_serie() {
    var bandera = 1;
    var tipo_gestion = $("#tipo_gestion").val();
    var documento = $("#documento").val();
    var serie = $("#serie").val();
    var folio = $("#folio").val();
    var cp = $("#codigo_postal").val();
    var leyenda = $("#leyenda_documento").val();

    if (documento == 0) {
        $("#documento").addClass('is-invalid');
        bandera = 2;
    }
    if (folio == 0 || folio.length == 0) {
        $("#folio").addClass('is-invalid');
        bandera = 2;
    }
    if (cp == 0 || cp.length == 0) {
        $("#codigo_postal").addClass('is-invalid');
        bandera = 2;
    }

    if (bandera == 1) {
        $.ajax({
            cache: false,
            url: 'componentes/configuraciones/gestionar_series.php',
            type: 'POST',
            dataType: 'html',
            data: { 'documento': documento, 'serie': serie, 'folio': folio, 'cp': cp, 'leyenda': leyenda, 'tipo_gestion': tipo_gestion },
        }).done(function (resultado) {
            if (resultado == "repetido") {
                Swal.fire({
                    icon: "warning",
                    title: "Serie existente",
                    html: "Ya existe la serie para este tipo de documento",
                    showConfirmButton: false,
                    timer: 1500
                });
            }
            if (resultado == "correcto") {
                Swal.fire({
                    icon: "success",
                    title: "Serie registrada",
                    html: "Se registro/actualizo correctamente la serie",
                    showConfirmButton: false,
                    timer: 1500
                }).then(function () {
                    window.location = 'configuraciones.php';
                });
            }
            if (resultado == "incorrecto") {
                Swal.fire({
                    icon: "error",
                    title: "Serie no registrada",
                    html: "Ocurrio un error al registrar/actualizar la serie",
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    }
}

/*
function buscar_cp()
{
    var codigo = $("#codigo_postal").val();
    if(codigo.length == 5)
    {
        $.ajax({
            cache: false,
            url : 'componentes/administrador/buscar_estado.php',
            type : 'POST',
            dataType : 'html',
            data : { 'codigo': codigo},
        }).done(function(resultado){
            $("#estado").html(resultado);
            $("#estado").prop('disabled', false);

            $.ajax({
                cache: false,
                url : 'componentes/administrador/buscar_municipio.php',
                type : 'POST',
                dataType : 'html',
                data : { 'codigo': codigo},
            }).done(function(resultado){
                $("#municipio").html(resultado);
                $("#municipio").prop('disabled', false);
            }); 
        });
    }
    else
    {
        $("#estado").html("<option value='0'>Estado</option>");
        $("#estado").prop('disabled', 'disabled');
        $("#municipio").html("<option value='0'>Municipio</option>");
        $("#municipio").prop('disabled', 'disabled');
    }
}
    */

function actualizar_estatus_documento(id_partida, estatus) {
    $.ajax({
        cache: false,
        url: 'componentes/configuraciones/actualizar_estatus_documento.php',
        type: 'POST',
        dataType: 'html',
        data: { 'id_partida': id_partida, 'estatus': estatus },
    }).done(function (resultado) {
        Swal.fire({
            icon: 'success',
            title: 'Estatus actualizado!',
            showConfirmButton: false,
            timer: 2000
        }).then(function () {
            window.location = 'configuraciones.php';
        });
    });
}

function editar_documento(id_partida, id_documento, serie, folio, codigo_postal, leyenda) {
    $("#leyenda").html("Modificar datos del documento y serie");
    $("#tipo_gestion").val(id_partida);
    $("#documento").val(id_documento);
    $("#serie").val(serie);
    $("#folio").val(folio);
    $("#leyenda_documento").val(leyenda);
    $("#codigo_postal").val(codigo_postal);

    $.ajax({
        cache: false,
        url: 'componentes/configuraciones/buscar_estado.php',
        type: 'POST',
        dataType: 'html',
        data: { 'codigo': codigo_postal },
    }).done(function (resultado) {
        $("#estado").html(resultado);
        $("#estado").prop('disabled', false);

        $.ajax({
            cache: false,
            url: 'componentes/configuraciones/buscar_municipio.php',
            type: 'POST',
            dataType: 'html',
            data: { 'codigo': codigo_postal },
        }).done(function (resultado) {
            $("#municipio").html(resultado);
            $("#municipio").prop('disabled', false);
        });
    });

    $("html, body").animate({ scrollTop: 0 }, 600);
}

function guardar_formato_cotizacion() {
    var id_cotizacion = $("#id_cotizacion").val();
    var terminos = $("#terminos").val();
    var observaciones = $('#observaciones').val();
    var calidad = $('#calidad').val();
    var nombre_autoriza = $('#nombre_autoriza').val();
    var mostrar_cb = $('#mostrar_cb').prop('checked');
    var hoja = document.querySelector("#hoja_membretada");
    var hoja2 = $('#hoja_membretada').val();
    var firma = document.querySelector("#firma_autoriza");
    var firma2 = $('#firma_autoriza').val();

    if (hoja2 != "" && hoja.files[0].size > 150000) {
        Swal.fire({
            icon: "error",
            title: "Peso no permitido",
            html: "La imagen de la hoja membretada no debe superar los 150kb de peso",
            showConfirmButton: false,
            timer: 1500
        });
        return false;
    }
    if (firma2 != "" && firma.files[0].size > 80000) {
        Swal.fire({
            icon: "error",
            title: "Peso no permitido",
            html: "La imagen de la firma no debe superar los 80kb de peso",
            showConfirmButton: false,
            timer: 1500
        });
        return false;
    }

    Swal.fire({
        title: 'Cargando informaci&oacute;n...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    let formData = new FormData();
    formData.append('id_cotizacion', id_cotizacion);
    formData.append('terminos', terminos);
    formData.append('observaciones', observaciones);
    formData.append('calidad', calidad);
    formData.append('nombre_autoriza', nombre_autoriza);
    formData.append('mostrar_cb', mostrar_cb);
    formData.append('hoja', hoja.files[0]); // En la posición 0; es decir, el primer elemento
    formData.append('firma', firma.files[0]); // En la posición 0; es decir, el primer elemento
    fetch("componentes/configuraciones/guardar_cotizacion.php", {
        method: 'POST',
        body: formData,
    })
        .then(resultado => resultado.text())
        .then(function (resultado) {
            Swal.close();
            if (resultado == 111) {
                Swal.fire({
                    icon: "success",
                    title: "Registro actualizado",
                    html: "Informaci&oacute;n cargada exitosamente!",
                    showConfirmButton: false,
                    timer: 1500
                }).then(function () {
                    window.location = 'configuraciones.php';
                });
            }
            if (resultado == 211) {
                Swal.fire({
                    icon: "warning",
                    title: "Registro incompleto",
                    html: "No se logr&oacute; cargar la hoja membretada",
                    showConfirmButton: false,
                    timer: 1500
                }).then(function () {
                    window.location = 'configuraciones.php';
                });
            }
            if (resultado == 121) {
                Swal.fire({
                    icon: "warning",
                    title: "Registro incompleto",
                    html: "No se logr&oacute; cargar la firma de autorizaci&oacute;n",
                    showConfirmButton: false,
                    timer: 1500
                }).then(function () {
                    window.location = 'configuraciones.php';
                });
            }
            if (resultado == 112) {
                Swal.fire({
                    icon: "warning",
                    title: "Registro incompleto",
                    html: "No se logr&oacute; actualizar la base de datos",
                    showConfirmButton: false,
                    timer: 1500
                });
            }
            if (resultado == 222) {
                Swal.fire({
                    icon: "error",
                    title: "Registro incorrecto",
                    html: "Ocurrio un error al registrar la informaci&oacute;n",
                    showConfirmButton: false,
                    timer: 1500
                });
            }
            hoja.value = null;
            firma.value = null;
        });
}

function eliminar_hf(id_cotizacion, tipo) {
    $.ajax({
        cache: false,
        url: 'componentes/configuraciones/eliminar_hf.php',
        type: 'POST',
        dataType: 'html',
        data: { 'id_cotizacion': id_cotizacion, 'tipo': tipo },
    }).done(function (resultado) {
        if (tipo == 1) {
            $("#mostrar_hoja").html("");
        }
        if (tipo == 2) {
            $("#mostrar_firma").html("");
        }
    });
}

function guardar_formato_minuta() {
    var id_minuta = $("#id_minuta").val();
    var calidad_minuta = $("#calidad_minuta").val();
    var hoja = document.querySelector("#hoja_membrete");
    var hoja2 = $('#hoja_membrete').val();

    if (hoja2 != "" && hoja.files[0].size > 150000) {
        Swal.fire({
            icon: "error",
            title: "Peso no permitido",
            html: "La imagen de la hoja membretada no debe superar los 150kb de peso",
            showConfirmButton: false,
            timer: 1500
        });
        return false;
    }

    Swal.fire({
        title: 'Cargando informaci&oacute;n...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    let formData = new FormData();
    formData.append('id_minuta', id_minuta);
    formData.append('calidad_minuta', calidad_minuta);
    formData.append('hoja', hoja.files[0]); // En la posición 0; es decir, el primer elemento
    fetch("componentes/configuraciones/guardar_minuta.php", {
        method: 'POST',
        body: formData,
    })
        .then(resultado => resultado.text())
        .then(function (resultado) {
            Swal.close();
            if (resultado == 11) {
                Swal.fire({
                    icon: "success",
                    title: "Registro actualizado",
                    html: "Informaci&oacute;n cargada exitosamente!",
                    showConfirmButton: false,
                    timer: 1500
                }).then(function () {
                    window.location = 'configuraciones.php';
                });
            }
            if (resultado == 21) {
                Swal.fire({
                    icon: "warning",
                    title: "Registro incompleto",
                    html: "No se logr&oacute; cargar la hoja membretada",
                    showConfirmButton: false,
                    timer: 1500
                }).then(function () {
                    window.location = 'configuraciones.php';
                });
            }
            if (resultado == 12) {
                Swal.fire({
                    icon: "warning",
                    title: "Registro incompleto",
                    html: "No se logr&oacute; cargar la firma de autorizaci&oacute;n",
                    showConfirmButton: false,
                    timer: 1500
                }).then(function () {
                    window.location = 'configuraciones.php';
                });
            }
            if (resultado == 22) {
                Swal.fire({
                    icon: "error",
                    title: "Registro incorrecto",
                    html: "Ocurrio un error al registrar la informaci&oacute;n",
                    showConfirmButton: false,
                    timer: 1500
                });
            }
            hoja.value = null;
        });
}

function eliminar_m(id_minuta) {
    $.ajax({
        cache: false,
        url: 'componentes/configuraciones/eliminar_m.php',
        type: 'POST',
        dataType: 'html',
        data: { 'id_minuta': id_minuta },
    }).done(function (resultado) {
        $("#mostrar_hoja_minuta").html("");
    });
}

function gestionar_expediente() {
    var bandera = 1;
    var tipo_gestion = $("#tipo_gestion").val();
    var nombre_documento = $("#nombre").val();
    var tipo_catalogo = $("#tipo_catalogo").val();
    var genera_vigencia = $("#vigencia").val();

    if (nombre_documento.length == 0) {
        $("#nombre").addClass('is-invalid');
        bandera = 2;
    }
    if (tipo_catalogo == 0) {
        $("#tipo_catalogo").addClass('is-invalid');
        bandera = 2;
    }
    if (genera_vigencia == 0) {
        $("#genera_vigencia").addClass('is-invalid');
        bandera = 2;
    }

    if (bandera == 1) {
        $.ajax({
            cache: false,
            url: 'componentes/configuraciones/gestionar_expediente.php',
            type: 'POST',
            dataType: 'html',
            data: { 'nombre_documento': nombre_documento, 'tipo_gestion': tipo_gestion, 'tipo_catalogo': tipo_catalogo, 'genera_vigencia': genera_vigencia },
        }).done(function (resultado) {
            if (resultado == "repetido") {
                Swal.fire({
                    icon: "warning",
                    title: "Documento existente",
                    html: "Ya existe un nombre de documento registrado",
                    showConfirmButton: false,
                    timer: 1500
                });
            }
            if (resultado == "correcto") {
                Swal.fire({
                    icon: "success",
                    title: "Documento registrado",
                    html: "Se registro/actualizo correctamente el documento en el expediente",
                    showConfirmButton: false,
                    timer: 1500
                }).then(function () {
                    window.location = 'configuraciones.php';
                });
            }
            if (resultado == "incorrecto") {
                Swal.fire({
                    icon: "error",
                    title: "Documento no registrado",
                    html: "Ocurrio un error al registrar/actualizar el documento",
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    }
}

function editar_documento_expediente(id_documento, nombre_documento, tipo_catalogo, genera_vigencia) {
    $("#leyenda").html("Modificar datos del documento");
    $("#tipo_gestion").val(id_documento);
    $("#nombre").val(nombre_documento);
    $("#tipo_catalogo").val(tipo_catalogo);
    $("#vigencia").val(genera_vigencia);

    $("html, body").animate({ scrollTop: 0 }, 600);
}

function actualizar_estatus_documento_expediente(id_documento, estatus) {
    $.ajax({
        cache: false,
        url: 'componentes/configuraciones/actualizar_estatus_documento_expediente.php',
        type: 'POST',
        dataType: 'html',
        data: { 'id_documento': id_documento, 'estatus': estatus },
    }).done(function (resultado) {
        Swal.fire({
            icon: 'success',
            title: 'Estatus actualizado!',
            showConfirmButton: false,
            timer: 2000
        }).then(function () {
            window.location = 'configuraciones.php';
        });
    });
}

function habilitar_banco() {
    $("#mrfc_banco").val($("#mnombre_banco").val());
    $("#mcuenta").prop("disabled", false);
    $("#mguardar_cuenta").prop("disabled", false);
}

function registrar_cuenta() {
    var cuenta = $("#mcuenta").val();
    var id_cliente = $("#mrfc").val();
    var rfc_banco = $("#mrfc_banco").val();
    var nombre_banco = $("#mnombre_banco option:selected").text();

    if (cuenta.length == 10 || cuenta.length == 16 || cuenta.length == 18) {
        $.ajax({
            cache: false,
            url: 'componentes/configuraciones/registrar_cuenta.php',
            type: 'POST',
            dataType: 'html',
            data: { 'cuenta': cuenta, 'id_cliente': id_cliente, 'rfc_banco': rfc_banco, 'nombre_banco': nombre_banco },
        }).done(function (resultado) {
            $("#tabla_cuentas").html(resultado);
        });
    }
    else {
        $("#mcuenta").addClass('is-invalid');
        return false;
    }
}

function actualizar_estatus_cuenta(id_cuenta, estatus) {
    $.ajax({
        cache: false,
        url: 'componentes/configuraciones/actualizar_estatus_cuenta.php',
        type: 'POST',
        dataType: 'html',
        data: { 'id_cuenta': id_cuenta, 'estatus': estatus },
    }).done(function (resultado) {
        Swal.fire({
            icon: 'success',
            title: 'Estatus actualizado!',
            showConfirmButton: false,
            timer: 2000
        }).then(function () {
            $("#tabla_cuentas").html(resultado);
        });
    });
}

function eliminar_cuenta(id_cuenta) {
    $.ajax({
        cache: false,
        url: 'componentes/configuraciones/eliminar_cuenta.php',
        type: 'POST',
        dataType: 'html',
        data: { 'id_cuenta': id_cuenta },
    }).done(function (resultado) {
        Swal.fire({
            icon: 'success',
            title: 'Cuenta eliminada!',
            showConfirmButton: false,
            timer: 2000
        }).then(function () {
            $("#tabla_cuentas").html(resultado);
        });
    });
}