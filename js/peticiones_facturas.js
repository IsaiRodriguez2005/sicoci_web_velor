function consultar_datos()
{
    var id_cliente = $("#cliente").val();

    if(id_cliente > 0)
    {
        $.ajax({
            cache: false,
            url : 'componentes/facturas/consultar_datos.php',
            type : 'POST',
            dataType : 'json',
            data : { 'id_cliente': id_cliente},
        }).done(function(resultado){
            $("#rfc").val(resultado.campo.rfc);
            $("#domicilio").val(resultado.campo.calle + " " + resultado.campo.no_exterior + " " + resultado.campo.no_interior + " COL. " + resultado.campo.colonia + " CP " + resultado.campo.codigo_postal + ", " + resultado.campo.municipio + ", " + resultado.campo.estado + ". " + resultado.campo.pais);
            $("#regimen").val("[" + resultado.campo.regimen_fiscal + "] " + resultado.campo.nombre_regimen);
            $("#uso_cfdi > option[value='"+resultado.campo.uso_cfdi+"']").prop("selected","selected");
            $("#metodo_pago > option[value='"+resultado.campo.metodo_pago+"']").prop("selected","selected");
            $("#forma_pago > option[value='"+resultado.campo.forma_pago+"']").prop("selected","selected");
            $("#dias_credito").val(resultado.campo.dias_credito);
            $("#datos_cliente").show();
            $("#datos_cliente2").show();

            if(resultado.campo.rfc == "XAXX010101000")
            {
                $("#periodicidad").prop("disabled", false);
                $("#meses").prop("disabled", false);
                $("#anio").prop("disabled", false);
            }
            else
            {
                $("#periodicidad").prop("disabled", "disabled");
                $("#periodicidad > option[value='0']").prop("selected","selected");
                $("#meses").prop("disabled", "disabled");
                $("#meses > option[value='0']").prop("selected","selected");
                $("#anio").prop("disabled", "disabled");
                $("#anio").val("");
            }
        }); 
    }
    else
    {
        $("#rfc").val("");
        $("#domicilio").val("");
        $("#regimen").val("");
        $("#dias_credito").val("");
        $("#uso_cfdi > option[value='0']").prop("selected","selected");
        $("#metodo_pago > option[value='0']").prop("selected","selected");
        $("#forma_pago > option[value='0']").prop("selected","selected");
        $("#periodicidad").prop("disabled", "disabled");
        $("#periodicidad > option[value='0']").prop("selected","selected");
        $("#meses").prop("disabled", "disabled");
        $("#meses > option[value='0']").prop("selected","selected");
        $("#anio").prop("disabled", "disabled");
        $("#anio").val("");
        $("#datos_cliente").hide();
        $("#datos_cliente2").hide();
    }
}

function activar_forma()
{
    var metodo = $("#metodo_pago").val();
    if(metodo == "PPD")
    {
        $("#forma_pago").html("<option value='99'>[99] POR DEFINIR</option>");
    }
    else
    {
        $.ajax({
            cache: false,
            url : 'componentes/facturas/cargar_forma_pago.php',
            type : 'POST',
            dataType : 'html',
        }).done(function(resultado){
            $("#forma_pago").html(resultado);
        }); 
    }
}

function activar_moneda()
{
    var moneda = $("#moneda").val();
    if(moneda == "MXN")
    {
        $("#tipo_cambio").val("0");
        $("#tipo_cambio").prop("disabled","disabled");
    }
    else
    {
        $("#tipo_cambio").val("1");
        $("#tipo_cambio").prop("disabled",false);
    }
}

function buscar_concepto()
{
    var id_concepto = $("#concepto_sat").val();

    if(id_concepto == 0)
    {
        $("#precio").val("");
        $("#iva").val("");
        $("#retencion").val("");
        $("#clave_sat_concepto").val("");
        $("#clave_sat_medida").val("");
        $("#iva_exento").prop("checked",false);
    }
    else
    {
        $.ajax({
            cache: false,
            url : 'componentes/facturas/consultar_concepto.php',
            type : 'POST',
            dataType : 'json',
            data : { 'id_concepto': id_concepto},
        }).done(function(resultado){
            $("#precio").val(resultado.campo.precio_unitario);
            $("#clave_sat_concepto").val(resultado.campo.clave_concepto);
            $("#clave_sat_medida").val(resultado.campo.clave_unidad);
            $("#iva").val(resultado.campo.porcentaje_iva);
            $("#retencion").val(resultado.campo.porcentaje_retencion);
            if(resultado.campo.exento_iva == 1)
            {
                $("#iva_exento").prop("checked",false);
                $("#iva").prop("disabled",false);
                $("#retencion").prop("disabled",false);
            }
            else
            {
                $("#iva_exento").prop("checked","checked");
                $("#iva").prop("disabled","disabled");
                $("#retencion").prop("disabled","disabled");
            }
            $("#precio").removeClass('is-invalid');
            $("#iva").removeClass('is-invalid');
            $("#retencion").removeClass('is-invalid');
        }); 
    }
}

function mostrar_historial_facturas()
{
    var finicial = $("#fecha_inicial").val();
    var ffinal = $("#fecha_final").val();

    if(finicial.length == 0)
    {
        $("#fecha_inicial").addClass('is-invalid');
        return false;
    }
    if(ffinal.length == 0)
    {
        $("#fecha_final").addClass('is-invalid');
        return false;
    }

    Swal.fire({
        title: 'Cargando facturas...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    $.ajax({
        cache: false,
        url : 'componentes/facturas/historial_facturas.php',
        type : 'POST',
        dataType : 'html',
        data : {},
    }).done(function(resultado){
        $("#historial_facturas").html(resultado);
        Swal.close();
    }); 
}

function nueva_factura(nombre)
{
    $.ajax({
        cache: false,
        url : 'componentes/facturas/consultar_serie.php',
        type : 'POST',
        dataType : 'html',
    }).done(function(resultado){
        if(resultado == "error")
        {
            Swal.fire({
                icon: 'warning',
                title: 'No hay series fiscales registradas',
                html: 'Dirigete a <b>CONFIGURACIONES > SERIES FISCALES</b> para crear una',
                showConfirmButton: true
            });
        }
        else
        {
            $("#tabla_series_facturas").html(resultado);
            $("#series_facturas").modal("show");
        }
    });
}

function aperturar_factura(id_partida, id_documento)
{
    $.ajax({
        cache: false,
        url : 'componentes/facturas/aperturar_folio.php',
        type : 'POST',
        dataType : 'json',
        data : { 'id_partida': id_partida, 'id_documento': id_documento},
    }).done(function(resultado){
        if(resultado.estatus == "error")
        {
            Swal.fire({
                icon: 'warning',
                title: 'Error al aperturar el folio',
                html: "Comunicate al &aacute;rea de soporte",
                showConfirmButton: true
            });
        }
        else
        {
            $('#tabla_facturas tr:first').after(resultado.registro_tr);
            $("#series_facturas").modal("hide");
            
            $.ajax({
                cache: false,
                url : 'componentes/facturas/mostrar_factura.php',
                type : 'POST',
                dataType : 'html',
                data : { 'id_documento': id_documento, 'folio_factura': resultado.folio, 'serie_factura': resultado.serie},
            }).done(function(resultado2){
                $("#ver_factura").html(resultado2);
                $("#e_ffactura").html(resultado.serie + " " + resultado.folio);
                $("#editar_factura").modal("show");
            });
        }
    });
}

function editar_factura(id_documento, folio_factura, serie)
{
    $.ajax({
        cache: false,
        url : 'componentes/facturas/mostrar_factura.php',
        type : 'POST',
        dataType : 'html',
        data : { 'id_documento': id_documento, 'folio_factura': folio_factura, 'serie_factura': serie},
    }).done(function(resultado){
        $("#e_ffactura").html(serie + " " + folio_factura);
        $("#ver_factura").html(resultado);
        $("#editar_factura").modal("show");
    });
}

function eliminar_relacion(id_partida, id_documento, folio_factura)
{
    $.ajax({
        cache: false,
        url : 'componentes/facturas/eliminar_relacion.php',
        type : 'POST',
        dataType : 'html',
        data : { 'id_partida': id_partida, 'id_documento': id_documento, 'folio_factura': folio_factura},
    }).done(function(resultado){
        if(resultado == "error")
        {
            Swal.fire({
                icon: 'error',
                title: 'No se logr&oacute; eliminar la relaci&oacute;n',
                html: "Comunicate al &aacute;rea de soporte",
                showConfirmButton: true
            });
        }
        else
        {
            $("#mostrar_uuid_relacionados").html(resultado);
        }
    });
}

function validar_iva_exento()
{
    if($("#iva_exento").prop("checked"))
    {
        $("#iva").val("");
        $("#retencion").val("");
        $("#iva").prop("disabled", "disabled");
        $("#retencion").prop("disabled", "disabled");
    }
    else
    {
        $("#iva").prop("disabled", false);
        $("#retencion").prop("disabled", false);
    }
}

function agregar_producto()
{
    var bandera = 1;
    var bandera_partida = $("#e_partida").val();
    var id_documento = $("#e_id_documento").val();
    var folio_factura = $("#e_folio_factura").val();
    var id_producto = $("#concepto_sat").val();
    var cantidad = $("#cantidad").val();
    var clave_concepto = $("#clave_sat_concepto").val();
    var clave_medida = $("#clave_sat_medida").val();
    var precio = $("#precio").val();
    var iva = $("#iva").val();
    var retencion = $("#retencion").val();
    var descripcion = $("#descripcion_concepto").val();
    if($("#iva_exento").prop("checked"))
    {
        //Si exento
        var iva_exento = 2;
    }
    else
    {
        //No exento
        var iva_exento = 1;
    }
    
    if(id_producto == 0)
    {
        $("#concepto_sat").addClass('is-invalid');
        bandera = 2;
    }
    if(cantidad == 0)
    {
        $("#cantidad").addClass('is-invalid');
        bandera = 2;
    }
    if(precio == 0)
    {
        $("#precio").addClass('is-invalid');
        bandera = 2;
    }
    if(descripcion == 0)
    {
        $("#descripcion_concepto").addClass('is-invalid');
        bandera = 2;
    }
    if(iva_exento == 1 && iva.length == 0)
    {
        $("#iva").addClass('is-invalid');
        bandera = 2;
    }

    if(bandera == 1)
    {
        $.ajax({
            cache: false,
            url : 'componentes/facturas/agregar_producto.php',
            type : 'POST',
            dataType : 'html',
            data : { 'id_producto': id_producto, 'cantidad': cantidad, 'clave_concepto': clave_concepto, 'clave_medida': clave_medida, 'precio': precio, 'iva': iva, 'retencion': retencion, 'descripcion': descripcion, 'id_documento': id_documento, 'folio_factura': folio_factura, 'iva_exento': iva_exento, 'bandera_partida': bandera_partida},
        }).done(function(resultado){
            if(resultado == "error")
            {
                Swal.fire({
                    icon: 'error',
                    title: 'Ocurrio un error al agregar/modificar el producto',
                    timer: 1000,
                    showConfirmButton: false
                });
            }
            else
            {
                Swal.fire({
                    icon: 'success',
                    title: 'Registrado',
                    timer: 1000,
                    showConfirmButton: false
                });
                $("#tabla_productos").html(resultado);
                $("#e_partida").val("0");
                $("#concepto_sat").val("0");
                $("#cantidad").val("");
                $("#clave_sat_concepto").val("");
                $("#clave_sat_medida").val("");
                $("#precio").val("");
                $("#iva").val("");
                $("#retencion").val("");
                $("#descripcion_concepto").val("");

                calcular_totales();
            }
        });
    }
}

function activar_isr()
{
    if($("#mostrar_isr").prop("checked"))
    {
        $("#porcentaje_isr").prop("disabled", false);
    }
    else
    {
        $("#porcentaje_isr").val("0.00");
        $("#porcentaje_isr").prop("disabled", "disabled");
        $("#retencion_isr").val("0.00");

        var acuTot = parseFloat($("#subtotal").val()) + parseFloat($("#iva_total").val()) - parseFloat($("#retencion_iva").val()) - parseFloat($("#retencion_isr").val());
        $("#total").val(acuTot)
    }
}

function eliminar_producto(partida, id_documento, folio_factura)
{
    $.ajax({
        cache: false,
        url : 'componentes/facturas/eliminar_producto.php',
        type : 'POST',
        dataType : 'html',
        data : { 'partida': partida, 'id_documento': id_documento, 'folio_factura': folio_factura},
    }).done(function(resultado){
        if(resultado == "error")
        {
            Swal.fire({
                icon: 'error',
                title: 'Ocurrio un error al eliminar el concepto',
                timer: 1000,
                showConfirmButton: false
            });
        }
        else
        {    
            Swal.fire({
                icon: 'success',
                title: 'Concepto eliminado',
                timer: 1000,
                showConfirmButton: false
            });
            $("#tabla_productos").html(resultado);

            calcular_totales();
        }
    });
}

function editar_producto(no_partida, id_producto, cantidad, clave_sat_servicio, clave_sat_unidad, descripcion, precio_unitario, porcentaje_iva, porcentaje_retencion, exento_iva)
{
    $("#e_partida").val(no_partida);
    $("#concepto_sat").val(id_producto);
    $("#cantidad").val(cantidad);
    $("#clave_sat_concepto").val(clave_sat_servicio);
    $("#clave_sat_medida").val(clave_sat_unidad);
    $("#precio").val(precio_unitario);
    $("#iva").val(porcentaje_iva);
    $("#retencion").val(porcentaje_retencion);
    $("#descripcion_concepto").val(descripcion);
    if(exento_iva == 1)
    {
        $("#iva_exento").prop("checked", false);
    }
    else
    {
        $("#iva_exento").prop("checked", "checked");
    }
}

function calcular_totales()
{
    var id_documento = $("#e_id_documento").val();
    var folio_factura = $("#e_folio_factura").val();
    var porciento_isr = parseFloat($("#porcentaje_isr").val());
    var acuIsr = 0;
    var acuTot = 0;

    $.ajax({
        cache: false,
        url : 'componentes/facturas/consultar_totales.php',
        type : 'POST',
        dataType : 'json',
        data : { 'id_documento': id_documento, 'folio_factura': folio_factura},
    }).done(function(resultado){
        if(porciento_isr != 0)
        {
            acuIsr = (parseFloat(porciento_isr) / 100) * resultado.campo.acuImp;
        }

        acuTot = parseFloat(resultado.campo.acuImp) + parseFloat(resultado.campo.acuIva) - parseFloat(resultado.campo.acuRet) - parseFloat(acuIsr);

        $("#subtotal").val(resultado.campo.acuImp);
        $("#iva_total").val(resultado.campo.acuIva);
        $("#retencion_iva").val(resultado.campo.acuRet);
        $("#total").val(acuTot);
    });
}

function calcular_isr()
{
    var porcentaje_isr = $("#porcentaje_isr").val();

    if(porcentaje_isr.length == 0)
    {
        $("#retencion_isr").val("0.00")

        var acuTot = parseFloat($("#subtotal").val()) + parseFloat($("#iva_total").val()) - parseFloat($("#retencion_iva").val()) - parseFloat($("#retencion_isr").val());
        $("#total").val(acuTot)
    }
    else
    {
        var acuImp = $("#subtotal").val();
        var acuIsr = (parseFloat(porcentaje_isr) / 100) * acuImp;
        $("#retencion_isr").val(acuIsr.toFixed(2))

        var acuTot = parseFloat($("#subtotal").val()) + parseFloat($("#iva_total").val()) - parseFloat($("#retencion_iva").val()) - parseFloat($("#retencion_isr").val());
        $("#total").val(acuTot.toFixed(2))
    }
}

function validar_factura(bandera)
{
    var id_documento = $("#e_id_documento").val();
    var folio_factura = $("#e_folio_factura").val();
    var serie_factura = $("#e_serie_factura").val();
    //Sección cliente
    var id_cliente = $("#cliente").val();
    var rfc = $("#rfc").val();
    //Sección informacion general
    var uso_cfdi = $("#uso_cfdi").val();
    var metodo_pago = $("#metodo_pago").val();
    var forma_pago = $("#forma_pago").val();
    var moneda = $("#moneda").val();
    var tipo_cambio = $("#tipo_cambio").val();
    var dias_credito = $("#dias_credito").val();
    var referencia = $("#referencia").val();
    var observaciones = $("#observaciones").val();
    var exportacion = $("#exportacion").val();
    var periodicidad = $("#periodicidad").val();
    var meses = $("#meses").val();
    var anio = $("#anio").val();
    //Sección totales
    var subtotal = $("#subtotal").val();
    var iva_total = $("#iva_total").val();
    var retencion_iva = $("#retencion_iva").val();
    var retencion_isr = $("#retencion_isr").val();
    var porcentaje_isr = $("#porcentaje_isr").val();
    var total = $("#total").val();

    if(id_cliente == 0)
    {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: 'Selecciona un CLIENTE',
            showConfirmButton: true
        });
        return false;
    }
    if(uso_cfdi == 0)
    {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: 'Selecciona un USO CFDI',
            showConfirmButton: true
        });
        return false;
    }
    if(metodo_pago == 0)
    {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: 'Selecciona un METODO DE PAGO',
            showConfirmButton: true
        });
        return false;
    }
    if(forma_pago == 0)
    {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: 'Selecciona una FORMA DE PAGO',
            showConfirmButton: true
        });
        return false;
    }
    if(moneda == 0)
    {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: 'Selecciona una MONEDA',
            showConfirmButton: true
        });
        return false;
    }
    if(moneda != "MXN" && tipo_cambio == 0)
    {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: 'Ingresa el TIPO DE CAMBIO de la moneda',
            showConfirmButton: true
        });
        return false;
    }
    if(dias_credito.length == 0)
    {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: 'Ingresa los DIAS DE CREDITO',
            showConfirmButton: true
        });
        return false;
    }
    if(rfc == "XAXX010101000" && periodicidad == 0)
    {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: 'Selecciona la PERIODICIDAD en la pestaña INFORMACION GLOBAL Y EXPORTACION',
            showConfirmButton: true
        });
        return false;
    }
    if(rfc == "XAXX010101000" && meses == 0)
    {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: 'Selecciona el MES en la pestaña INFORMACION GLOBAL Y EXPORTACION',
            showConfirmButton: true
        });
        return false;
    }
    if(rfc == "XAXX010101000" && anio.length == 0)
    {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: 'Ingresa el A&Ntilde;O en la pestaña INFORMACION GLOBAL Y EXPORTACION',
            showConfirmButton: true
        });
        return false;
    }
    if(parseFloat(total) == 0)
    {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: 'Debes ingresar por lo menos un CONCEPTO a la factura',
            showConfirmButton: true
        });
        return false;
    }

    $.ajax({
        cache: false,
        url : 'componentes/facturas/guardar_factura.php',
        type : 'POST',
        dataType : 'html',
        data : { 'id_documento': id_documento, 'folio_factura': folio_factura, 'serie_factura': serie_factura, 'id_cliente': id_cliente, 'uso_cfdi': uso_cfdi, 'metodo_pago': metodo_pago, 'forma_pago': forma_pago, 'moneda': moneda, 'tipo_cambio': tipo_cambio, 'dias_credito': dias_credito, 'referencia': referencia, 'observaciones': observaciones, 'exportacion': exportacion, 'periodicidad': periodicidad, 'meses': meses, 'anio': anio, 'subtotal': subtotal, 'iva_total': iva_total, 'retencion_iva': retencion_iva, 'retencion_isr': retencion_isr, 'porcentaje_isr': porcentaje_isr, 'total': total},
    }).done(function(resultado){
        if(resultado == "error")
        {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: 'Ocurrio un error al guardar la factura, contacta a soporte',
                showConfirmButton: true
            }); 
        }
        else
        {
            if(bandera == 1)
            {
                Swal.fire({
                    icon: 'success',
                    title: 'Proforma guardada',
                    timer: 1000,
                    showConfirmButton: false
                });

                $("#tr_"+id_documento+folio_factura).html(resultado);
                $("#editar_factura").modal("hide");
            }
            else
            {

            }
        }
    });
}

function check_caracter(e)
{
    if(e.charCode == 34 || e.charCode == 38 || e.charCode == 39 || e.charCode == 124)
    {
        return false;
    }
}

function ver_pdf(id_documento, folio_factura, serie_factura)
{
    //var ruta = "componentes/ver_factura.php?id_liquidacion=" + folio + "&id_folios=" + id_folios;
    //$("#folio_liq").html(serie + " " + folio);
    //$("#ruta_pdf").prop("src", ruta);
    $("#pdf_ffactura").html(serie_factura + " " + folio_factura);
    $("#ver_pdf_factura").modal("show");
}














