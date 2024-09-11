function buscar_cp()
{
    var codigo = $("#codigo_postal").val();
    var rfc = $("#rfc").val();
    if(codigo.length == 5 && rfc != "XEXX010101000")
    {
        $.ajax({
            cache: false,
            url : 'componentes/catalogos/buscar_colonia.php',
            type : 'POST',
            dataType : 'html',
            data : { 'codigo': codigo},
        }).done(function(resultado){
            $("#colonia").html(resultado);
            $("#colonia").prop('disabled', false);

            $.ajax({
                cache: false,
                url : 'componentes/catalogos/buscar_estado.php',
                type : 'POST',
                dataType : 'html',
                data : { 'codigo': codigo},
            }).done(function(resultado){
                $("#estado").html(resultado);
                $("#estado").prop('disabled', false);

                $.ajax({
                    cache: false,
                    url : 'componentes/catalogos/buscar_municipio.php',
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

function gestionar_personal()
{
    var tipo_gestion = $('#tipo_gestion').val();
    
    var fiscal = "";
    var no_fisico = $('input:radio[id=no_fisico]:checked').val()
    var fisico = $('input:radio[id=fisico]:checked').val()
    if(no_fisico == 1)
    {
        fiscal = 1;
    }
    else
    {
        fiscal = 2;
    }
    
    var nombre_personal = $('#nombre_personal').val();
    var tipo_personal = $('#tipo_personal').val();

    // ojoooo ------------------------------------------
    if (!tipo_personal) {
        
        $("#tipo_personal").addClass('is-invalid');
            Swal.fire({
                icon: "error",
                title: "Debes especificar el tipo de personal",
                showConfirmButton: false,
                timer: 1500
            });

        return;
    }


    var calle = $('#calle').val();
    var no_exterior = $('#no_exterior').val();
    var no_interior = $('#no_interior').val();
    var codigo_postal = $('#codigo_postal').val();
    var tipo_colonia = $("#colonia_oculta").val();
    if(tipo_colonia == 2)
    {
        var colonia = $('#colonia_text').val();
    }
    else
    {
        var colonia = $('#colonia').val();
    }
    var estado = $('#estado').val();
    var municipio = $('#municipio').val();
    var pais = $('#pais').val();
    var correo = $('#correo').val();
    var telefono = $('#telefono').val();
    var estatus = 1;



    if(fiscal == 1)
    {
        if(nombre_personal.length == 0)
        {
            $("#nombre_personal").addClass('is-invalid');
            Swal.fire({
                icon: "error",
                title: "Debes especificar el nombre del personal",
                showConfirmButton: false,
                timer: 1500
            });

            return false;
        }
    }
    else
    {
        if(nombre_personal.length == 0)
        {
            $("#nombre_personal").addClass('is-invalid');
            Swal.fire({
                icon: "error",
                title: "Debes especificar el nombre del personal",
                showConfirmButton: false,
                timer: 1500
            });

            return false;
        }
    }


    Swal.fire({
        title: 'Registrando personal...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    $.ajax({
        cache: false,
        url : "componentes/catalogos/registrar_personal.php",
        type : 'POST',
        dataType : 'html',
        data : { 'nombre_personal': nombre_personal, 'tipo_personal': tipo_personal , 'calle': calle, 'no_exterior': no_exterior, 'no_interior': no_interior, 'codigo_postal': codigo_postal, 'colonia': colonia, 'estado': estado, 'municipio': municipio, 'pais': pais, 'correo': correo, 'telefono': telefono, 'estatus': estatus, 'id_personal': tipo_gestion},
    }).done(function(resultado){

        if(resultado == "ok")
        {
            Swal.fire({
                icon: "success",
                title: "Personal Registrado",
                html: "La informaci&oacute;n se registro exitosamente",
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                window.location = 'personal.php';
            });
        }
        else if(resultado == "actualizado")
        {
            Swal.fire({
                icon: "success",
                title: "Personal Actializado",
                html: "La informaci&oacute;n se registro exitosamente",
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                window.location = 'personal.php';
            });
        }
        else if(resultado == "error")
        {
            Swal.fire({
                icon: "warning",
                title: "Personal No Registrado",
                showConfirmButton: false,
                timer: 2000
            });
        }
        else
        {
            Swal.fire({
                icon: "warning",
                title: "El Personal Ya Existe",
                showConfirmButton: false,
                timer: 2000
            });
        }
    });
}

function ver_catalogo()
{
    $.ajax({
        cache: false,
        url : 'componentes/catalogos/cargar_personal.php',
        type : 'POST',
        dataType : 'html',
    }).done(function(resultado){
        $("#vista_personal").html(resultado);
        $("#modal_personal").modal("show");
    }); 
}
function actualizar_estatus_personal(id_personal, estatus)
{
    $.ajax({
        cache: false,
        url : 'componentes/catalogos/actualizar_estatus_personal.php',
        type : 'POST',
        dataType : 'html',
        data : { 'id_personal': id_personal, 'estatus': estatus},
    }).done(function(resultado){
        Swal.fire({
            icon: 'success',
            title: 'Estatus actualizado!',
            showConfirmButton: false,
            timer: 2000
        }).then(function() {
            window.location = 'personal.php';
        });
    }); 
}

function editar_personal(id_personal, nombre_personal, tipo_personal, calle, exterior, interior, codigo_postal, colonia, municipio, estado, pais, correo, telefono)
{

    $("#leyenda").html("Modificar datos del personal");
    $("#tipo_gestion").val(id_personal);
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

    $("#correo").prop("disabled",false);
    $("#calle").prop("disabled",false);
    $("#no_exterior").prop("disabled",false);
    $("#no_interior").prop("disabled",false);
    $("#codigo_postal").prop("disabled",false);
    $("#municipio").prop("disabled",false);
    $("#estado").prop("disabled",false);
    $("#pais").prop("disabled",false); 
    if(colonia.length > 4)
    {
        var cadena = "";
        cadena = '<div class="input-group-prepend"> <span class="input-group-text"><i class="fas fa-city"></i></span> </div>';
        cadena += '<input type="text" class="form-control" placeholder="Escribe colonia" id="colonia_text" maxlength="100" onfocus="resetear(&quot;colonia_text&quot;)">';
        cadena += '&nbsp; <button type="button" class="btn btn-info" onclick="colonia_select();" title="Capturar colonia"><i class="fas fa-edit"></i></button>';
        $("#dato_colonia").html(cadena);
        $("#colonia_text").val(colonia);
        $("#colonia_oculta").val("2");
    }
    else
    {
        var cadena = "";
        cadena = '<div class="input-group-prepend"> <span class="input-group-text"><i class="fas fa-city"></i></span> </div>';
        cadena += '<select class="form-control" id="colonia" onfocus="resetear(&quot;colonia&quot;)" disabled> <option value="0">Colonia</option> </select>';
        cadena += '&nbsp; <button type="button" class="btn btn-info" onclick="colonia_text();" title="Capturar colonia"><i class="fas fa-edit"></i></button>';
        $("#dato_colonia").html(cadena);
        $("#colonia_oculta").val("1");  
        $.ajax({
            cache: false,
            url : 'componentes/catalogos/buscar_colonia.php',
            type : 'POST',
            dataType : 'html',
            data : { 'codigo': codigo_postal},
        }).done(function(resultado){
            $("#colonia").html(resultado);
            $("#colonia").prop('disabled', false);
        });
        $("#colonia > option[value='"+colonia+"']").prop("selected","selected");
    }   
    var cadena_pais = "";
    cadena_pais = '<div class="input-group-prepend"> <span class="input-group-text"><i class="fas fa-city"></i></span> </div>';
    cadena_pais += '<select class="form-control" id="pais" onfocus="resetear(&quot;pais&quot;)" disabled><option value="0">Pais</option></select>';
    $("#dato_pais").html(cadena_pais);  
    $.ajax({
        cache: false,
        url : 'componentes/catalogos/buscar_estado.php',
        type : 'POST',
        dataType : 'html',
        data : { 'codigo': codigo_postal},
    }).done(function(resultado){
        $("#estado").html(resultado);
        $("#estado").prop('disabled', false);   
        $.ajax({
            cache: false,
            url : 'componentes/catalogos/buscar_municipio.php',
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

    $("#modal_personal").modal("hide");
}
