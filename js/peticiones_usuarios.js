function generar_password() {
    let result = "";
    const abc =
        "a b c d e f g h i j k l m n o p q r s t u v w x y z A B C D E F G H I J K L M N O P Q R S T U V X Y Z 1 2 3 4 5 6 7 8 9".split(
            " "
        ); // Espacios para convertir cara letra a un elemento de un array
    for (i = 0; i <= 7; i++) {
        const random = Math.floor(Math.random() * abc.length);
        result += abc[random];
    }
    $("#password").val(result);
}

function ver_catalogo() {
    $.ajax({
        cache: false,
        url: "componentes/catalogos/cargar/cargar_usuarios.php",
        type: "POST",
        dataType: "html",
    }).done(function (resultado) {
        $("#vista").html(resultado);
        $("#modal_cargar").modal("show");
    });
}

function gestionar_usuario() {
    var bandera = 1;
    var tipo_gestion = $("#tipo_gestion").val();
    var id_usuario = $("#id_usuario").val();
    var id_personal = $("#id_terapeuta").val();
    var nombre = $("#nombre").val();
    var correo = $("#correo").val();
    var password = $("#password").val();
    var configuraciones = 2;
    var agenda = 2;
    var clientes = 2;
    var usuarios = 2;
    var productos = 2;
    var proveedores = 2;
    var personal = 2;
    var tickets = 2;
    var facturas = 2;
    var pago_proveedores = 2;
    var reportes = 2;
    var dash_directivo = 2;

    if (nombre.length == 0) {
        $("#nombre").addClass("is-invalid");
        bandera = 2;
    }
    if (correo.length == 0) {
        $("#correo").addClass("is-invalid");
        bandera = 2;
    }
    if (password.length == 0) {
        $("#password").addClass("is-invalid");
        bandera = 2;
    }
    if ($("#configuraciones").prop("checked")) {
        configuraciones = 1;
    } else {
        configuraciones = 2;
    }
    if ($("#agenda").prop("checked")) {
        agenda = 1;
    } else {
        agenda = 2;
    }
    if ($("#clientes").prop("checked")) {
        clientes = 1;
    } else {
        clientes = 2;
    }
    if ($("#usuarios").prop("checked")) {
        usuarios = 1;
    } else {
        usuarios = 2;
    }
    if ($("#productos").prop("checked")) {
        productos = 1;
    } else {
        productos = 2;
    }
    if ($("#proveedores").prop("checked")) {
        proveedores = 1;
    } else {
        proveedores = 2;
    }
    if ($("#personal").prop("checked")) {
        personal = 1;
    } else {
        personal = 2;
    }
    if ($("#tickets").prop("checked")) {
        tickets = 1;
    } else {
        tickets = 2;
    }
    if ($("#facturacion").prop("checked")) {
        facturacion = 1;
    } else {
        facturacion = 2;
    }
    if ($("#pago_proveedores").prop("checked")) {
        pago_proveedores = 1;
    } else {
        pago_proveedores = 2;
    }
    if ($("#reportes").prop("checked")) {
        reportes = 1;
    } else {
        reportes = 2;
    }
    if ($("#dash_directivo").prop("checked")) {
        dash_directivo = 1;
    } else {
        dash_directivo = 2;
    }
    Swal.fire({
        title: "Registrando Usuario...",
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });

    if (bandera == 1) {
        if (tipo_gestion == 0) {
            var url_destino = "componentes/catalogos/registrar_usuario.php";
        } else {
            var url_destino = "componentes/catalogos/actualizar_usuario.php";
        }

        $.ajax({
            cache: false,
            url: url_destino,
            type: "POST",
            dataType: "html",
            data: {
                nombre: nombre,
                correo: correo,
                password: password,
                id_usuario: id_usuario,
                id_personal: id_personal,
                configuraciones: configuraciones,
                agenda: agenda,
                clientes: clientes,
                usuarios: usuarios,
                productos: productos,
                proveedores: proveedores,
                personal: personal,
                tickets: tickets,
                facturacion: facturacion,
                pago_proveedores: pago_proveedores,
                reportes: reportes,
                dash_directivo: dash_directivo,
            },
        }).done(function (resultado) {
            //console.log(resultado)
            if (Number(resultado) == 1) {
                Swal.fire({
                    icon: "error",
                    title: "El Usuario Ya Tiene Personal Asociado",
                    html: "Verifica el usuario",
                    showConfirmButton: true,
                    //timer: 2000
                }).then(function () {
                    window.location = "usuarios.php";
                });
            } else {
                Swal.fire({
                    icon: "success",
                    title: "Usuario Registrado",
                    html: "La informaci&oacute;n se registro exitosamente",
                    showConfirmButton: true,
                    //timer: 2000
                }).then(function () {
                    window.location = "usuarios.php";
                });
            }
        });
    }
}

function recargar_tabla_usuarios(id_usuario) {
    const tipo = 2;
    $.ajax({
        cache: false,
        url: "componentes/catalogos/cargar/cargar_usuarios.php",
        type: "POST",
        dataType: "html",
        data: { id_usuario: id_usuario, tipo: tipo },
    }).done(function (resultado) {
        $("#tr_usuario_" + id_usuario).replaceWith(resultado);
    });
}

function actualizar_estatus_usuario(id_usuario, estatus) {
    Swal.fire({
        title: "Actualizando Estatus Usuario...",
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });
    $.ajax({
        cache: false,
        url: "componentes/catalogos/actualizar_estatus_usuario.php",
        type: "POST",
        dataType: "html",
        data: { id_usuario: id_usuario, estatus: estatus },
    }).done(function (resultado) {
        Swal.fire({
            icon: "success",
            title: "Estatus actualizado!",
            showConfirmButton: true,
            timer: 2000,
        }).then(function () {
            recargar_tabla_usuarios(id_usuario);
        });
    });
}

function editar_usuario(
    id_usuario,
    nombre,
    correo,
    password,
    configuraciones,
    agenda,
    clientes,
    usuarios,
    productos,
    proveedores,
    personal,
    tickets,
    facturacion,
    pago_proveedores,
    reportes,
    dash_directivo
) {
    $("#leyenda").html("Modificar datos del usuario");
    $("#tipo_gestion").val(id_usuario);
    $("#id_usuario").val(id_usuario);
    $("#nombre").val(nombre);
    $("#correo").val(correo);
    $("#password").val(password);

    if (configuraciones == 1) {
        $("#configuraciones").prop("checked", "checked");
    } else {
        $("#configuraciones").prop("checked", false);
    }
    if (agenda == 1) {
        $("#agenda").prop("checked", "checked");
    } else {
        $("#agenda").prop("checked", false);
    }
    if (clientes == 1) {
        $("#clientes").prop("checked", "checked");
    } else {
        $("#clientes").prop("checked", false);
    }
    if (usuarios == 1) {
        $("#usuarios").prop("checked", "checked");
    } else {
        $("#usuarios").prop("checked", false);
    }
    if (productos == 1) {
        $("#productos").prop("checked", "checked");
    } else {
        $("#productos").prop("checked", false);
    }
    if (proveedores == 1) {
        $("#proveedores").prop("checked", "checked");
    } else {
        $("#proveedores").prop("checked", false);
    }
    if (personal == 1) {
        $("#personal").prop("checked", "checked");
    } else {
        $("#personal").prop("checked", false);
    }
    if (tickets == 1) {
        $("#tickets").prop("checked", "checked");
    } else {
        $("#tickets").prop("checked", false);
    }
    if (facturacion == 1) {
        $("#facturacion").prop("checked", "checked");
    } else {
        $("#facturacion").prop("checked", false);
    }
    if (pago_proveedores == 1) {
        $("#pago_proveedores").prop("checked", "checked");
    } else {
        $("#pago_proveedores").prop("checked", false);
    }
    if (reportes == 1) {
        $("#reportes").prop("checked", "checked");
    } else {
        $("#reportes").prop("checked", false);
    }
    if (dash_directivo == 1) {
        $("#dash_directivo").prop("checked", "checked");
    } else {
        $("#dash_directivo").prop("checked", false);
    }

    $("#modal_cargar").modal("hide");
}

function cargar_terapeutas() {
    $.ajax({
        cache: false,
        url: "componentes/catalogos/cargar/options/options_terapeutas.php",
        type: "POST",
        dataType: "html",
        data: {},
    }).done(function (resultado) {
        $("#id_terapeuta").html(resultado);
    });
}

function getDatosTerapeuta() {
    const id_terapeuta = $("#id_terapeuta").val();

    if (id_terapeuta == 0) {
        $("#nombre").prop("disabled", false);
        $("#correo").prop("disabled", false);
        $("#nombre").val("");
        $("#correo").val("");
        return;
    } else {
        $("#nombre").prop("disabled", true);
        $("#correo").prop("disabled", true);
    }
    $.ajax({
        cache: false,
        url: "componentes/catalogos/cargar/cargar_datos_terapeuta.php",
        type: "POST",
        dataType: "json",
        data: { id_terapeuta: id_terapeuta },
    }).done(function (resultado) {
        const { nombre_personal, correo } = resultado[0];
        if (resultado) {
            $("#nombre").val(nombre_personal);
            $("#correo").val(correo);
        } else {
            $("#nombre").val("");
            $("#correo").val("");
        }
    });
}
