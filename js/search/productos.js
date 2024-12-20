
/*
    ejemplo de funciones para cargar info en buscador

    async function cargar_productos_servicios() {
    const resProductos = await $.ajax({
        cache: false,
        url: 'componentes/tickets/productos_servicios/productos.php',
        type: 'POST',
        dataType: 'json',
        data: {
            funcion: 'cargarProdutos',
        },
    });

    return resProductos;
}

    const data_productos = await cargar_productos_servicios();

    const { productos, success } = data_productos;

    if (success) {
        const ul = $('#suggestions');
        ul.empty();
        productos.forEach((producto) => {
            const li = `<li data-value="${producto.id_producto}">${producto.nombre}</li>`;
            ul.append(li);
        });
    }
*/


//TODO: funciones del buscador de [productos]

let currentFocus = -1; // Índice del elemento actualmente enfocado

function filtrar_lista() {
    const input = $('#search');
    const filter = input.val().toLowerCase();
    const ul = $('#suggestions');
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

    // Reinicia el índice si la lista está visible
    if (hasVisibleItems) {
        currentFocus = -1;
    }
}
// Navegar y seleccionar sugerencias usando el teclado
$("#search").on("keydown", function (e) {
    const ul = $("#suggestions");
    const li = ul.find("li:visible"); // Solo elementos visibles
    const liCount = li.length;

    if (liCount === 0) return; // Salir si no hay elementos visibles

    if (e.key === "ArrowDown") {
        e.preventDefault(); // Evita el scroll de la página
        currentFocus = (currentFocus + 1) % liCount; // Incrementa el índice
        highlightSuggestion(li); // Resalta el elemento actual
    } else if (e.key === "ArrowUp") {
        e.preventDefault();
        currentFocus = (currentFocus - 1 + liCount) % liCount; // Decrementa el índice
        highlightSuggestion(li);
    } else if (e.key === "Enter") {
        e.preventDefault();
        if (currentFocus > -1) {
            $(li[currentFocus]).click(); // Simula el clic en el elemento actual
        }
    }
});

// Resaltar sugerencia seleccionada
function highlightSuggestion(li) {
    li.removeClass("active"); // Elimina la clase activa de todos
    if (currentFocus > -1) {
        $(li[currentFocus]).addClass("active"); // Agrega clase activa al actual
        // Opcional: Desplaza la lista para mostrar el elemento seleccionado
        li[currentFocus].scrollIntoView({ block: "nearest" });
    }
}

$("#suggestions").on("click keydown", "li", function (e) {
    if (e.type === "click" || (e.type === "keydown" && e.key === "Enter")) {
        // Aquí manejamos tanto el clic como la tecla Enter

        const input = $("#search");
        const inputCantidad = $("#cantidad_producto");

        const hiddenInput = $("#id_producto");
        const selectedValue = $(this).data("value"); // Obtiene el valor del atributo 'data-value' del <li>
        const selectedText = $(this).text(); // Obtiene el texto del <li>

        // Establecer valores en los inputs correspondientes
        input.val(selectedText);
        hiddenInput.val(selectedValue);

        // Ocultar sugerencias
        $("#suggestions").addClass("hidden");

        inputCantidad.select();
    }
});

// Manejar "Enter" en el input de cantidad
$("#cantidad_producto").on("keydown", function (e) {
    if (e.key === "Enter") {
        e.preventDefault(); // Prevenir comportamiento predeterminado
        agregarProducto(); // Llamar a la función agregarProducto
    }
});

// Opcional: Ocultar la lista de sugerencias si se hace clic fuera del contenedor
$(document).on("click", function (e) {
    if (!$(e.target).closest(".search-container").length) {
        $("#suggestions").addClass("hidden");
    }
});


/*

    html USO: 

    <div class="row">
        <!-- Selector de producto -->
        <div class="form-group col-md-9 mb-2 text-center">
            <label class="form-label font-weight-bold d-block">Producto</label>
            <div class="search-container">
                <input type="hidden" id="id_producto">
                <input type="text" id="search" placeholder="Buscar..."
                    oninput="filtrar_lista()" class="form-control" />
                <ul id="suggestions" class="suggestions hidden">
                    <!-- Sugerencias dinámicas -->
                </ul>
            </div>
        </div>
        <!-- Input de cantidad -->
        <div class="form-group col-md-3 mb-3 text-center">
            <label class="form-label font-weight-bold d-block">Cantidad</label>
            <input type="text" name="cantidad" class="form-control text-center"
                value="1" style="font-size: 1.5rem;" id="cantidad_producto">
        </div>

    </div>
*/