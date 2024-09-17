function buscar_cp()
{
    console.log('hola');
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

function activar_forma()
{
    var metodo = $("#metodo_pago").val();

    if(metodo == "PPD")
    {
        cadena = '<option value="99">[99] POR DEFINIR </option>';
        $("#forma_pago").html(cadena);
    }
    else
    {
        $.ajax({
            cache: false,
            url : 'componentes/catalogos/cargar_forma_pago.php',
            type : 'POST',
            dataType : 'html',
        }).done(function(resultado){
            $("#forma_pago").html(resultado);
        }); 
    }
}

function gestionar_cliente( )
{

    var tipo_gestion = $('#tipo_gestion').val();
    var nombre_social = $('#nombre_social').val();
    var correo = $('#correo').val();
    var telefono = $('#telefono').val();

    if(nombre_social.length == 0)
    {
        $("#nombre_social").addClass('is-invalid');
        Swal.fire({
            icon: "error",
            title: "Debes especificar el nombre del cliente/paciente",
            showConfirmButton: false,
            timer: 1500
        });

        return false;
    }

    /*
    if(fiscal == 1)
    {
        if(nombre_social.length == 0)
        {
            $("#nombre_social").addClass('is-invalid');
            Swal.fire({
                icon: "error",
                title: "Debes especificar el nombre del cliente/paciente",
                showConfirmButton: false,
                timer: 1500
            });

            return false;
        }
    }
    else
    {
        if(rfc.length != 13)
        {
            $("#rfc").addClass('is-invalid');
            Swal.fire({
                icon: "error",
                title: "Debes especificar un RFC valido",
                showConfirmButton: false,
                timer: 1500
            });

            return false;
        }
        if(nombre_social.length == 0)
        {
            $("#nombre_social").addClass('is-invalid');
            Swal.fire({
                icon: "error",
                title: "Debes especificar el nombre del cliente/paciente",
                showConfirmButton: false,
                timer: 1500
            });

            return false;
        }
        if(calle.length == 0)
        {
            $("#calle").addClass('is-invalid');
            Swal.fire({
                icon: "error",
                title: "Debes especificar el nombre de la calle",
                showConfirmButton: false,
                timer: 1500
            });

            return false;
        }
        if(no_exterior.length == 0)
        {
            $("#no_exterior").addClass('is-invalid');
            Swal.fire({
                icon: "error",
                title: "Debes especificar el n&uacute;mero exterior del domicilio",
                showConfirmButton: false,
                timer: 1500
            });

            return false;
        }
        if(codigo_postal.length == 0)
        {
            $("#codigo_postal").addClass('is-invalid');
            Swal.fire({
                icon: "error",
                title: "Debes especificar el c&oacute;digo postal del domicilio",
                showConfirmButton: false,
                timer: 1500
            });

            return false;
        }
        if(colonia.length == 0 || colonia == 0)
        {
            if(tipo_colonia == 2 || extranjero == 2)
            {
                $("#colonia_text").addClass('is-invalid');
            }
            else
            {
                $("#colonia").addClass('is-invalid');
            }
            
            Swal.fire({
                icon: "error",
                title: "Debes especificar la colonia del domicilio",
                showConfirmButton: false,
                timer: 1500
            });

            return false;
        }
        if(estado == 0 || estado.length == 0)
        {
            $("#estado").addClass('is-invalid');
            Swal.fire({
                icon: "error",
                title: "Debes especificar el estado",
                showConfirmButton: false,
                timer: 1500
            });

            return false;
        }
        if(municipio == 0 || municipio.length == 0)
        {
            $("#municipio").addClass('is-invalid');
            Swal.fire({
                icon: "error",
                title: "Debes especificar el municipio",
                showConfirmButton: false,
                timer: 1500
            });

            return false;
        }
        if(regimen == 0)
        {
            $("#regimen").addClass('is-invalid');
            Swal.fire({
                icon: "error",
                title: "Debes especificar el regimen fiscal",
                showConfirmButton: false,
                timer: 1500
            });

            return false;
        }
        if(uso_cfdi == 0)
        {
            $("#uso_cfdi").addClass('is-invalid');
            Swal.fire({
                icon: "error",
                title: "Debes especificar el uso cfdi",
                showConfirmButton: false,
                timer: 1500
            });

            return false;
        }
        if(metodo_pago == 0)
        {
            $("#metodo_pago").addClass('is-invalid');
            Swal.fire({
                icon: "error",
                title: "Debes especificar el metodo de pago",
                showConfirmButton: false,
                timer: 1500
            });

            return false;
        }
        if(forma_pago == 0)
        {
            $("#forma_pago").addClass('is-invalid');
            Swal.fire({
                icon: "error",
                title: "Debes especificar la forma de pago",
                showConfirmButton: false,
                timer: 1500
            });

            return false;
        }
    }
        */

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
        url : "componentes/catalogos/registrar_cliente.php",
        type : 'POST',
        dataType : 'html',
        data : { 'nombre_social': nombre_social, 'correo': correo, 'telefono': telefono, 'id_cliente': tipo_gestion},
    }).done(function(resultado){
        if(resultado == "ok")
        {
            Swal.fire({
                icon: "success",
                title: "Cliente/Paciente Registrado",
                html: "La informaci&oacute;n se registro exitosamente",
                showConfirmButton: false,
                timer: 2000
            }).then(function() {
                window.location = "clientes.php" ;
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
        else
        {
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

function ver_catalogo()
{
    $.ajax({
        cache: false,
        url : 'componentes/catalogos/cargar_clientes.php',
        type : 'POST',
        dataType : 'html',
    }).done(function(resultado){
        $("#vista_clientes").html(resultado);
        $("#modal_clientes").modal("show");
    }); 
}
function actualizar_estatus_cliente(id_cliente, estatus)
{
    $.ajax({
        cache: false,
        url : 'componentes/catalogos/actualizar_estatus_cliente.php',
        type : 'POST',
        dataType : 'html',
        data : { 'id_cliente': id_cliente, 'estatus': estatus},
    }).done(function(resultado){
        Swal.fire({
            icon: 'success',
            title: 'Estatus actualizado!',
            showConfirmButton: false,
            timer: 2000
        }).then(function() {
            window.location = 'clientes.php';
        });
    }); 
}

function editar_cliente(id_cliente, nombre_social, correo, telefono)
{
    $("#leyenda").html("Modificar datos del cliente");
    $("#tipo_gestion").val(id_cliente);
    $("#nombre_social").val(nombre_social);
    $("#correo").val(correo);
    $("#telefono").val(telefono);

    /*
    if(tipo_cliente == 1)
    {
        $("#no_fisico").prop("checked","checked");
        $("#fisico").prop("checked",false);
        
        $("#rfc").prop("disabled","disabled");
        $("#nombre_social").prop("disabled",false);
        $("#correo").prop("disabled",false);
        $("#telefono").prop("disabled",false);
        $("#calle").prop("disabled","disabled");
        $("#no_exterior").prop("disabled","disabled");
        $("#no_interior").prop("disabled","disabled");
        $("#codigo_postal").prop("disabled","disabled");
        $("#colonia").prop("disabled","disabled");
        $("#municipio").prop("disabled","disabled");
        $("#estado").prop("disabled","disabled");
        $("#pais").prop("disabled","disabled");
        $("#regimen").prop("disabled","disabled");
        $("#metodo_pago").prop("disabled","disabled");
        $("#forma_pago").prop("disabled","disabled");
        $("#uso_cfdi").prop("disabled","disabled");
    }
    else
    {
        $("#no_fisico").prop("checked", false);
        $("#fisico").prop("checked", "checked");
        
        $("#rfc").prop("disabled",false);
        $("#nombre_social").prop("disabled",false);
        $("#correo").prop("disabled",false);
        $("#telefono").prop("disabled",false);
        $("#calle").prop("disabled",false);
        $("#no_exterior").prop("disabled",false);
        $("#no_interior").prop("disabled",false);
        $("#codigo_postal").prop("disabled",false);
        $("#municipio").prop("disabled",false);
        $("#estado").prop("disabled",false);
        $("#pais").prop("disabled",false);
        $("#regimen").prop("disabled",false);
        $("#metodo_pago").prop("disabled",false);
        $("#forma_pago").prop("disabled",false);
        $("#uso_cfdi").prop("disabled",false);

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

        if(metodo_pago == "PPD")
            {
                cadena = '<option value="99">[99] POR DEFINIR </option>';
                $("#forma_pago").html(cadena);
            }
            else
            {
                $.ajax({
                    cache: false,
                    url : 'componentes/catalogos/cargar_forma_pago.php',
                    type : 'POST',
                    dataType : 'html',
                }).done(function(resultado){
                    $("#forma_pago").html(resultado);
                    $("#forma_pago > option[value='"+forma_pago+"']").prop("selected","selected");
                }); 
            }
    }
    */

    $("#modal_clientes").modal("hide");
}

function cargar_perfil(id_cliente, nombre_cliente)
{
    $("#perfil_id_cliente").val(id_cliente);
    $("#perfil_nombre_cliente").val(nombre_cliente);
    $("#modal_perfiles").modal("show");
}