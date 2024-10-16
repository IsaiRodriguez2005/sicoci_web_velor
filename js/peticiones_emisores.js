function gestionar_emisor()
{
    var bandera = 1;
    var tipo_colonia = $("#colonia_oculta").val();
    var tipo_gestion = $('#tipo_gestion').val();
    var rfc = $('#rfc').val();
    var nombre_social = $('#nombre_social').val();
    var nombre_comercial = $('#nombre_comercial').val();
    var calle = $('#calle').val();
    var no_exterior = $('#no_exterior').val();
    var no_interior = $('#no_interior').val();
    var codigo_postal = $('#codigo_postal').val();
    if(tipo_colonia == 1)
    {
        var colonia = $('#colonia').val();
    }
    else
    {
        var colonia = $('#colonia_text').val();
    }
    var estado = $('#estado').val();
    var municipio = $('#municipio').val();
    var pais = $('#pais').val();
    var regimen = $('#regimen').val();
    var correo = $('#correo').val();
    var telefono = $('#telefono').val();
    var sitio_web = $('#sitio_web').val();

    if(rfc.length == 0 || rfc.length < 12 || rfc == "")
    {
        $("#rfc").addClass('is-invalid');
        bandera = 2;
    }
    if(nombre_social.length == 0)
    {
        $("#nombre_social").addClass('is-invalid');
        bandera = 2;
    }
    if(nombre_comercial.length == 0)
    {
        $("#nombre_comercial").addClass('is-invalid');
        bandera = 2;
    }
    if(calle.length == 0)
    {
        $("#calle").addClass('is-invalid');
        bandera = 2;
    }
    if(no_exterior.length == 0)
    {
        $("#no_exterior").addClass('is-invalid');
        bandera = 2;
    }
    if(codigo_postal.length == 0)
    {
        $("#codigo_postal").addClass('is-invalid');
        bandera = 2;
    }
    if(colonia.length == 0 || colonia == 0)
    {
        if(tipo_colonia == 1)
        {
            $("#colonia").addClass('is-invalid');
        }
        else
        {
            $("#colonia_text").addClass('is-invalid');
        }
        bandera = 2;
    }
    if(estado == 0)
    {
        $("#estado").addClass('is-invalid');
        bandera = 2;
    }
    if(municipio == 0)
    {
        $("#municipio").addClass('is-invalid');
        bandera = 2;
    }
    if(pais == 0)
    {
        $("#pais").addClass('is-invalid');
        bandera = 2;
    }
    if(regimen == 0)
    {
        $("#regimen").addClass('is-invalid');
        bandera = 2;
    }

    if(bandera == 1)
    {
        Swal.fire({
            title: 'Registrando emisor...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            }
        });

        if(tipo_gestion == 0)
        {
            var url_destino = "componentes/administrador/registrar_emisor.php";
        }
        else
        {
            var url_destino = "componentes/administrador/actualizar_emisor.php";
        }

        $.ajax({
            cache: false,
            url : url_destino,
            type : 'POST',
            dataType : 'html',
            data : { 'rfc': rfc, 'nombre_social': nombre_social, 'nombre_comercial': nombre_comercial, 'calle': calle, 'no_exterior': no_exterior, 'no_interior': no_interior, 'codigo_postal': codigo_postal, 'colonia': colonia, 'estado': estado, 'municipio': municipio, 'pais': pais, 'regimen': regimen, 'correo': correo, 'telefono': telefono, 'sitio_web': sitio_web, 'id_emisor': tipo_gestion},
        }).done(function(resultado){
            Swal.fire({
                icon: "success",
                title: "Emisor Registrado",
                html: "La informaci&oacute;n se registro exitosamente",
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                window.location = "emisores.php";
            });
        });
    }
}

function buscar_cp()
{
    var codigo = $("#codigo_postal").val();
    if(codigo.length == 5)
    {
        //$("#colonia").prop('disabled', false);
        $.ajax({
            cache: false,
            url : 'componentes/administrador/buscar_colonia.php',
            type : 'POST',
            dataType : 'html',
            data : { 'codigo': codigo},
        }).done(function(resultado){
            $("#colonia").html(resultado);
            $("#colonia").prop('disabled', false);

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

                    $("#pais").html("<option value='MEX'>MEXICO</option>");
                    $("#pais").prop('disabled', false);
                }); 
            }); 
        });
    }
    else
    {
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

function colonia_text()
{
    var cadena = "";
    cadena = '<div class="input-group-prepend"> <span class="input-group-text"><i class="fas fa-city"></i></span> </div>';
    cadena += '<input type="text" class="form-control" placeholder="Escribe colonia" id="colonia_text" maxlength="100" onfocus="resetear(&quot;colonia_text&quot;)">';
    cadena += '&nbsp; <button type="button" class="btn btn-info" onclick="colonia_select();" title="Capturar colonia"><i class="fas fa-edit"></i></button>';
    $("#dato_colonia").html(cadena);
    $("#colonia_oculta").val("2");
}

function colonia_select()
{
    var cadena = "";
    cadena = '<div class="input-group-prepend"> <span class="input-group-text"><i class="fas fa-city"></i></span> </div>';
    cadena += '<select class="form-control" id="colonia" onfocus="resetear(&quot;colonia&quot;)" disabled> <option value="0">Colonia</option> </select>';
    cadena += '&nbsp; <button type="button" class="btn btn-info" onclick="colonia_text();" title="Capturar colonia"><i class="fas fa-edit"></i></button>';
    $("#dato_colonia").html(cadena);
    $("#colonia_oculta").val("1");
}

function actualizar_estatus_emisor(id_emisor, estatus)
{
    $.ajax({
        cache: false,
        url : 'componentes/administrador/actualizar_estatus_emisor.php',
        type : 'POST',
        dataType : 'html',
        data : { 'id_emisor': id_emisor, 'estatus': estatus},
    }).done(function(resultado){
        Swal.fire({
            icon: 'success',
            title: 'Estatus actualizado!',
            showConfirmButton: false,
            timer: 2000
        }).then(function() {
            window.location = 'emisores.php';
        });
    }); 
}

function editar_emisor(id_emisor, rfc, nombre_social, nombre_comercial, calle, exterior, interior, codigo_postal, colonia, regimen, telefono, correo, sitio_web)
{
    $("#leyenda").html("Modificar datos del emisor");
    $("#tipo_gestion").val(id_emisor);
    $("#rfc").val(rfc);
    $("#nombre_social").val(nombre_social);
    $("#nombre_comercial").val(nombre_comercial);
    $("#calle").val(calle);
    $("#no_exterior").val(exterior);
    $("#no_interior").val(interior);
    $("#codigo_postal").val(codigo_postal);
    if(colonia.length > 4)
    {
        var cadena = "";
        cadena = '<div class="input-group-prepend"> <span class="input-group-text"><i class="fas fa-city"></i></span> </div>';
        cadena += '<input type="text" class="form-control" placeholder="Escribe colonia" id="colonia_text" maxlength="100" onfocus="resetear(&quot;colonia_text&quot;)" value="'+colonia+'">';
        cadena += '&nbsp; <button type="button" class="btn btn-info" onclick="colonia_select();" title="Capturar colonia"><i class="fas fa-edit"></i></button>';
        $("#dato_colonia").html(cadena);
        $("#colonia_oculta").val("2");
    }
    else
    {
        $.ajax({
            cache: false,
            url : 'componentes/administrador/buscar_colonia.php',
            type : 'POST',
            dataType : 'html',
            data : { 'codigo': codigo_postal},
        }).done(function(resultado){
            $("#colonia").html(resultado);
            $("#colonia").prop('disabled', false);
            $("#colonia > option[value='"+colonia+"']").attr("selected","selected");

            $.ajax({
                cache: false,
                url : 'componentes/administrador/buscar_estado.php',
                type : 'POST',
                dataType : 'html',
                data : { 'codigo': codigo_postal},
            }).done(function(resultado){
                $("#estado").html(resultado);
                $("#estado").prop('disabled', false);

                $.ajax({
                    cache: false,
                    url : 'componentes/administrador/buscar_municipio.php',
                    type : 'POST',
                    dataType : 'html',
                    data : { 'codigo': codigo_postal},
                }).done(function(resultado){
                    $("#municipio").html(resultado);
                    $("#municipio").prop('disabled', false);

                    $("#pais").html("<option value='MEX'>MEXICO</option>");
                    $("#pais").prop('disabled', false);
                }); 
            }); 
        });        
    }
    $("#regimen > option[value='"+regimen+"']").attr("selected","selected");
    $("#telefono").val(telefono);
    $("#correo").val(correo);
    $("#sitio_web").val(sitio_web);
    $("#btn_emisor").html("Modificar datos");
    $("html, body").animate({ scrollTop: 0 }, 600);
}