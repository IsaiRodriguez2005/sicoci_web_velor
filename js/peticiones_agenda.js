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
    $("#cliente_form").val('');
    $("#fecha_cita_form").val('');
    $("#hora_cita_form").val('');
    $("#terapeuta_form").val('');
    $("#tipo_servicio_form").val('');
    $("#tipo_cita_form").val('');
    $("#consultorio_form").val('');
    $("#observaciones_form").val('');
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
function form_nueva_cita() {
    limpiar_form();
    $("#modal_nueva_cita").modal("show");
}

function recargar_hisorial_citas(folio, tipo) {
    //* esta funcion carga la ultima modificacion de la tabla de agenda
    //* la bariable [tipo] sifnifica que, si es 1 = apertura (se creo una nueva cita), 2 = actualizacion (se actualizo la informacion del registro)

    $.ajax({
        cache: false,
        url: 'componentes/catalogos/recargar/recargar_historial_citas.php',
        type: 'POST',
        dataType: 'html',
        data: { 'id_folio': folio }
    }).done(function (resultado) {
        if (tipo == 1) {
            $("#tabla_facturas tr:first").after(resultado);
        } else {
            $('#tr_' + folio).replaceWith(resultado);
        }
        limpiar_form();
        $("#modal_nueva_cita").modal("hide");
    });

}

function gestionar_cita() {

    const id_folio = $("#folio_gestion").val();
    const id_cliente = $("#id_cliente2").val();
    const fecha_cita = $("#fecha_cita_form").val();
    const hora_cita = $("#hora_cita_form").val();
    const id_terapeuta = $("#terapeuta_form").val();
    const tipo_servicio = $("#tipo_servicio_form").val();
    const id_consultorio = $("#consultorio_form").val();
    const tipo_cita = $("#tipo_cita_form").val();
    const observaciones = $("#observaciones_form").val();
    const fecha_hora_cita = `${fecha_cita} a las ${hora_cita}`;
    const camposFaltantes = [];

    if (id_cliente == '0') {
        $("#cliente_form").addClass('is-invalid');
        camposFaltantes.push('Cliente');
    }
    if (!fecha_cita) {
        $("#fecha_cita_form").addClass('is-invalid');
        camposFaltantes.push('Fecha de Cita');
    }

    if (!hora_cita) {
        $("#hora_cita_form").addClass('is-invalid');
        camposFaltantes.push('Hora de Cita');
    }

    if (!id_terapeuta) {
        $("#terapeuta_form").addClass('is-invalid');
        camposFaltantes.push('Terapeuta');
    }

    if (!tipo_servicio) {
        $("#tipo_servicio_form").addClass('is-invalid');
        camposFaltantes.push('Tipo de Servicio');
    }

    if (tipo_servicio == '1' && !id_consultorio) {
        $("#consultorio_form").addClass('is-invalid');
        camposFaltantes.push('Consultorio');
    }

    if (!id_folio || !id_cliente || !fecha_cita || !hora_cita || !id_terapeuta || !tipo_servicio || !id_consultorio) {
        Swal.fire({
            icon: "warning",
            title: "Por favor, complete los siguientes campos obligatorios:",
            html: camposFaltantes.join(", "),
            showConfirmButton: true,
        });
        return; //* Para la funcion
    }


    // pantallaCarga('Registrando cita...');
    pantallaCarga('Registrando cita...');

    $.ajax({
        cache: false,
        url: "componentes/catalogos/registrar/registrar_cita.php",
        type: 'POST',
        dataType: 'json',
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
        //* desestructuramos la respuesta
        // console.log(resultado);
        const { id_folio, actualizacion, mensaje, correo } = resultado;
        // console.log(correo);
        if (id_folio > 0 && actualizacion === false) {
            Swal.fire({
                icon: "success",
                title: "Cita Registrada",
                html: "La informaci&oacute;n se registro exitosamente",
                showConfirmButton: false,
                timer: 2000
            }).then(function () {
                //* agrega la tupla de la tabla
                recargar_hisorial_citas(id_folio, 1);
            });
        }
        else if (id_folio > 0 && actualizacion === true) {
            Swal.fire({
                icon: "success",
                title: "Cita Actializada",
                html: "La informaci&oacute;n se registro exitosamente",
                showConfirmButton: false,
                timer: 2000
            }).then(function () {
                //* agrega la tupla de la tabla
                recargar_hisorial_citas(id_folio, 2);
            });
        }
        else if (mensaje == "error") {
            Swal.fire({
                icon: "warning",
                title: "Cita No Registrada",
                showConfirmButton: false,
                timer: 2000
            });
        }
        //-------------VALIDACIONES DE EXISTENCIA EN LA AGENDA -------------
        else if (mensaje === 'clo') { // Validación para cliente y hora
            Swal.fire({
                icon: "warning",
                title: "Conflicto de Horario",
                text: `El cliente ya tiene una cita programada el ${fecha_hora_cita}.`,
                showConfirmButton: false,
                timer: 2000
            });
        } else if (mensaje === 'co') { // Validación para consultorio y hora
            Swal.fire({
                icon: "warning",
                title: "Consultorio Ocupado",
                text: `El consultorio ya está reservado para el ${fecha_hora_cita}.`,
                showConfirmButton: false,
                timer: 2000
            });
        } else if (mensaje === 'to') { // Validación para terapeuta y hora
            Swal.fire({
                icon: "warning",
                title: "Terapeuta Ocupado",
                text: `El terapeuta ya tiene una cita programada el ${fecha_hora_cita}.`,
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

    const id_terapeuta = $("#terapeuta_form").val();
    const fecha_cita = $("#fecha_cita_form").val();
    const folio_gestion = $("#folio_gestion").val();
    const hora_gestion = $("#hora_gestion").val();

    return $.ajax({
        cache: false,
        url: "componentes/catalogos/cargar/cargar_horarios_disponibles.php",
        type: 'POST',
        dataType: 'json',
        data: { 'fecha_hora': fecha_cita, 'id_terapeuta': id_terapeuta, 'folio_gestion': folio_gestion, 'hora_gestion': hora_gestion },
    }).done(function (resultado) {

        const { horarios, error, alert_error } = resultado;
        const select = $("#hora_cita_form");
        select.empty();

        //! posibles errores
        if (alert_error) {
            Swal.fire({
                icon: "error",
                title: "Error en la configuración",
                html: alert_error,
                confirmButtonText: "Entendido"
            }).then(function (){
                window.location = 'configuraciones.php';
            });
            return;
        }

        if (error) {
            select.append(`<option value="" disabled selected>${error}</option>`);
            return;
        }

        select.append('<option value="" disabled selected>Selecciona Hora de Cita</option>'); // Opción por defecto

        //* Insertar los horarios en el select
        horarios.forEach(horario => {
            select.append(`<option value="${horario}">${horario}</option>`);
        });
    });
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

        if (Number(data.total_registros) > 0) {
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
            cargar_horarios_disponibles().then(function () {
                $("#hora_cita").val(hora);

                cargar_consultorios_disponibles().then(function () {
                    $("#consultorio_form").val(citaData[0].id_consultorio);
                }).catch(function () {
                    console.error("Ocurrió un error al cargar los consultorios: ", error);
                });
            });

        }).catch(function (error) {
            console.error("Ocurrió un error al cargar los terapeutas: ", error);
        });

        $("#tipo_gestion").val(citaData[0].id_folio);
        $("#tipo_cita_form").val(citaData[0].tipo_cita);
        $("#tipo_servicio_form").val(citaData[0].tipo_servicio);
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
        console.log(resultado)
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

function enfermedades_form() {

    diabetes = $("#diabetes").prop('checked');
    hta = $("#hta").prop('checked');
    cancer = $("#cancer").prop('checked');
    enfermedades_reumaticas = $("#enfermedades_reumaticas").prop('checked');
    cardiopatias = $("#cardiopatias").prop('checked');
    cirugias = $("#cirugias").prop('checked');
    alergias = $("#alergias").prop('checked');
    transfusiones = $("#transfusiones").prop('checked');
    otros = $("#otros").prop('checked');

    if (diabetes) {

    }

}

function ver_pdf(id_folio, tipo_cita) {
    let ruta;

    if (tipo_cita == 2) {
        ruta = "componentes/formatos_pdf/ver_pdf_valoracion_pv.php?id_folio=" + id_folio;
    } else {
        ruta = "componentes/formatos_pdf/ver_pdf_valoracion_sb.php?id_folio=" + id_folio;
    }
    $("#ruta_pdf").prop("src", ruta);
    $("#pdf_fvaloracion").html(id_folio);
    $("#ver_pdf_valoracion").modal("show");
}


//TODO : Funciones de Funcion de [COBRO DE CITAS]

async function cobrar_cita(folio_cita) {
    //* en esta funcion se hace la peticion dependiendo de lo que necesite el cliente.
    const necesitaFactura = await necesita_factura();

    if (necesitaFactura) {
        console.log('necesito factura')
    } else {

        const resultado = await comprobar_existencia_serie_ticket();
        const { existe, seriesTickets } = resultado;

        if (existe) {
            if (seriesTickets.length == 1) {
                seleccionar_serie(seriesTickets[0].id)
            } else {
                $("#modal_opciones_series_tickets").modal("show");
                rellenar_tbody_seies_tickets(seriesTickets);
            }
        } else {
            console.error('No existen series de tickets')
        }

    }
}
async function necesita_factura() {
    //* esta funcion devuelve si el usuario o la compra necesita factura.
    const necesitaFactura = await Swal.fire({
        title: '¿Deseas confirmar esta acción?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ticket CON Factura',
        cancelButtonText: 'Ticket SIN Factua',
        reverseButtons: true,
    });

    if (necesitaFactura.isConfirmed) {
        return true;
    } else {
        return false;
    }
}
async function comprobar_existencia_serie_ticket() {
    //* esta funcion comprueba la existencia de las series
    try {
        const respuesta = await $.ajax({
            cache: false,
            url: 'componentes/tickets/existencias/series_tickets.php',
            type: 'POST',
            dataType: 'json',
            data: {
                'funcion': 'existenciaSeriesTickets',
                'id_documento': 7,
            },
        });

        const { success, folios } = respuesta;
        if (success) {
            return {
                existe: success,
                seriesTickets: folios
            };
        } else {
            return {
                existe: success
            };
        }
    } catch (error) {
        console.error('error al comprobar la existencia de la serie de tickets:', error);
        return {
            existe: success,
            error: 'error de solicitud'
        };
    }

}
function rellenar_tbody_seies_tickets(series) {

    const tbody = $("#mostrar_series_tickets");

    tbody.html('');

    series.forEach(ticket => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="text-center">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-sm" title="Seleccionar Serie" onclick="seleccionar_serie('${ticket.id}')">
                                <i class="fas fa-check"></i>
                            </button>
                        </div>
                    </td>
                    <td class="text-center">${ticket.id}</td>
                    <td class="text-center">${ticket.documento}</td>
                    <td class="text-center">${ticket.serie ? ticket.serie : 'N/A'}</td>
                    <td class="text-center">${ticket.codigoPostal}</td>
                    <td class="text-center">
                        <span class="badge ${ticket.estatus == 1 ? 'badge-success' : 'badge-secondary'}" style="width: 100%; color:white;">
                            ${ticket.estatus == 1 ? 'Activo' : 'Inactivo'}
                        </span>
                    </td>
        `;

        tbody.append(row);

        inizializar_tabla('tabla_series_tickets');
    });
}
function inizializar_tabla(idTabla) {
    if ($.fn.DataTable.isDataTable()) {
        $('#' + idTabla).DataTable().destroy('#' + idTabla);
    }
    $('#' + idTabla).DataTable({
        paging: true,
        lengthChange: false,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: true,
        responsive: true,
        deferRender: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
        }
    });
}

//TODO: funciones de series tickets

async function seleccionar_serie(idSerie) {

    $("#idSerieTicket").val(idSerie);
    const data_productos = await cargar_productos_servicios();

    const { productos, success } = data_productos;

    if (success) {
        const ul = $("#suggestions");
        ul.empty();
        productos.forEach(producto => {
            const li = `<li data-value="${producto.id_producto}">${producto.nombre}</li>`;
            ul.append(li);
        });
    }

    $("#modal_orden_compra_ticket").modal("show");
}

async function cargar_productos_servicios() {
    const resProductos = await $.ajax({
        cache: false,
        url: 'componentes/tickets/productos_servicios/productos.php',
        type: 'POST',
        dataType: 'json',
        data: {
            'funcion': 'cargarProdutos',
        },
    });

    return resProductos;
}

function filtrar_lista() {
    const input = $("#search");
    const filter = input.val().toLowerCase();
    const ul = $("#suggestions")
    const li = ul.find('li');
    let hasVisibleItems = false;

    li.each(function () {
        const textValue = $(this).text().toLowerCase(); // Obtén el texto del <li>
        if (textValue.indexOf(filter) > -1) {
            $(this).show(); // Muestra el <li> si coincide con el filtro
            hasVisibleItems = true;
        } else {
            $(this).hide(); // Oculta el <li> si no coincide
        }
    });

    // Oculta la lista completa si no hay elementos visibles
    ul.toggleClass('hidden', !hasVisibleItems);
}

// Maneja el evento de clic en un elemento <li> para seleccionar una sugerencia
$("#suggestions").on("click", "li", function () {
    const input = $("#search");
    const hiddenInput = $("#id_producto");
    const selectedValue = $(this).data("value"); // Obtiene el valor del atributo 'data-value' del <li>
    const selectedText = $(this).text(); // Obtiene el valor del atributo 'data-value' del <li>

    input.val(selectedText);
    hiddenInput.val(selectedValue);
    cargar_info_producto(selectedValue);

    $("#suggestions").addClass("hidden"); // Oculta la lista de sugerencias
});

// Opcional: Ocultar la lista de sugerencias si se hace clic fuera del contenedor
$(document).on("click", function (e) {
    if (!$(e.target).closest(".search-container").length) {
        $("#suggestions").addClass("hidden");
    }
});

function cargar_info_producto(idProducto) {
    //* esta funcion carga la info de los productos y dehabilita los campos para editarlos en la compra
    $.ajax({
        cache: false,
        url: 'componentes/tickets/productos_servicios/productos.php',
        type: 'POST',
        dataType: 'json',
        data: {
            'funcion': 'cargarProductoPorId',
            'id_producto': idProducto
        },
    }).done(function (resultado) {
        const { producto, success } = resultado;

        if (success) {
            const inputPrecioNeto = $("#precio_neto");
            const inputIVA = $("#iva");
            const inputPrecioBruto = $("#precio_bruto");

            inputPrecioNeto.prop("disabled", false);
            inputIVA.prop("disabled", false);
            inputPrecioBruto.prop("disabled", false);

            const {
                iva,
                precio
            } = producto[0];

            inputPrecioNeto.val(precio);
            inputIVA.val(iva);
            calcular_precio_bruto();
            app_total();
        }
    });
}

//! funciones de calculos para los tickets
function calcular_precio_bruto() {
    //* esta funcion, calcula de precio neto a precio bruto
    const iva = Number($("#iva").val());
    const precioNeto = Number($("#precio_neto").val());
    const inputPrecio = $("#precio_bruto");
    if (precioNeto && precioNeto != 0) {
        const precioBruto = ((100 + iva) * precioNeto) / 100;
        inputPrecio.val(precioBruto);
    } else {
        inputPrecio.val('');
    }

}
function calcular_precio_neto() {
    //* esat funcion, calcula de precio bruto a precio neto 
    const iva = Number($("#iva").val());
    const precioBruto = Number($("#precio_bruto").val());
    const inputPrecio = $("#precio_neto");

    if (precioBruto && precioBruto != 0) {
        const precioNeto = (100 * precioBruto) / (100 + iva);
        inputPrecio.val(precioNeto);
    } else {
        inputPrecio.val('');
    }
}
function app_iva() {
    //* esta funcion, se basa en dependiendo de lo que haya seleccionado o rellenado primero el usuario
    //* ya sea por pn o pb.

    const precioBruto = Number($("#precio_bruto").val());
    const precioNeto = Number($("#precio_neto").val());

    if (precioNeto && precioBruto) {
        calcular_precio_bruto();
    } else if (precioBruto) {
        calcular_precio_neto();
    } else if (precioNeto) {
        calcular_precio_bruto();
    }

}
function app_total() {
    const inputCantidad = $("#cantidad");
    const inputPrecioBruto = $("#precio_bruto");
    const inputTotal = $("#total");

    const cantidad = inputCantidad.val();
    const precioBruto = inputPrecioBruto.val();

    const total = cantidad * precioBruto;

    inputTotal.val(total);
}
