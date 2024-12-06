function getInfoDisabled(idInput, value, disbaled) {

    $("#" + idInput).val(String(value)).change();

    $("#" + idInput).prop('disabled', disbaled);
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
        //console.log(data.id_cliente)
        $("#id_cliente2").val(data.id_cliente);
    })
}

function mostrar_expedientes_clientes() {

    let id_cliente = $("#id_cliente2").val();
    //console.log(id_cliente)

    if (id_cliente) {

        $.ajax({
            cache: false,
            url: 'componentes/catalogos/cargar/cargar_expediente_cliente.php',
            type: 'POST',
            data: { 'id_cliente': id_cliente }
        }).done(function (data) {
            //console.log(data)
            $("#expedientes_citas").html(data);
        })

    } else {
        alert('El Usuario no existe');
    }

}

function mostrar_valoracion(folio, id_cliente) {
    cargarOcupaciones();
    $.ajax({
        cache: false,
        url: 'componentes/catalogos/cargar/cargar_valoracion.php',
        type: 'POST',
        dataType: 'json',
        data: { 'id_cliente': id_cliente, 'folio': folio }
    }).done(function (dataVal) {
        
        console.log(dataVal)
        
        $("#id_cliente_valoracion").val(dataVal[0].id_cliente);
        $("#folio").val(dataVal[0].folio);
        

        getInfoDisabled('nombre_valoracion',dataVal[0].nombre_cliente, true);
        getInfoDisabled('telefono_valoracion',dataVal[0].telefono, true);
        getInfoDisabled('fecha',dataVal[0].fecha_emision, true);
        getInfoDisabled('edad_valoracion', dataVal[0].edad, true);
        getInfoDisabled('ocupacion_valoracion', dataVal[0].id_ocupacion, true);
        getInfoDisabled('domicilio_valoracion', dataVal[0].domicilio, true);
        getInfoDisabled('telefono_valoracion', dataVal[0].telefono, true);
        getInfoDisabled('estado_civil_valoracion', dataVal[0].estado_civil, true);
        getInfoDisabled('toximanias_valoracion', dataVal[0].taxicomanias, true);
        getInfoDisabled('motivo_consulta_valoracion', dataVal[0].motivo_consulta, true);
        getInfoDisabled('act_fisica_valoracion', dataVal[0].actividad_fisica, true);

        getInfoDisabled('tension_art', dataVal[0].ta, true);
        getInfoDisabled('fc', dataVal[0].fc, true);
        getInfoDisabled('fr', dataVal[0].fr, true);
        getInfoDisabled('satO2', dataVal[0].oxigeno, true);
        getInfoDisabled('temp', dataVal[0].temperatura, true);
        getInfoDisabled('glucosa', dataVal[0].glucosa, true);
        getInfoDisabled('farmacos', dataVal[0].farmacos, true);
        getInfoDisabled('diagnosticoMedico', dataVal[0].diagnostico_medico, true);
        getInfoDisabled('escalaDolor', dataVal[0].escala_eva, true);
        $("#escalaValor").html(dataVal[0].escala_eva);

        $("#modal_valoracion").modal('show')
    })
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