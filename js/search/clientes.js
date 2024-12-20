/*
estas dos dunciones son las que se necesitan para cargar y formar los lis, al menos osn las bases

async function cargar_clientes_modal() {
    const clientes = await traer_clientes();
    const listUl = $('#list_clientes');

    let tuplas = '';

    clientes.forEach((clie) => {
        let { nombre, id } = clie;

        tuplas += `
        <li class="list-group-item" data-id="${id}">${nombre}</li>
    `;
    });

    listUl.html(tuplas);
}

async function traer_clientes() {
    try {
        const respuesta = await $.ajax({
            cache: false,
            url: 'componentes/tickets/peticiones/clientes.php',
            type: 'POST',
            dataType: 'json',
            data: {
                funcion: 'traerClientes',
            },
        });

        const { success, data, mensaje } = respuesta;

        if (!success) {
            throw new Error(mensaje);
        }

        return data;
    } catch (error) {
        console.error(error);
    }
}

*/

const searchInputClientes = document.getElementById("search_clientes");
const dropdown = document.getElementById("content_clientes");
const listUl = document.getElementById("list_clientes");

// Mostrar/ocultar dropdown
searchInputClientes.addEventListener("focus", () => {
    dropdown.style.display = "block";
});

searchInputClientes.addEventListener("blur", () => {
    setTimeout(() => (dropdown.style.display = "none"), 150);
});

// Filtrar resultados en tiempo real
searchInputClientes.addEventListener("input", function () {
    const filter = searchInputClientes.value.toLowerCase();
    const listItems = document.querySelectorAll("#list_clientes li"); // Seleccionar dinámicamente
    let hasResults = false;

    listItems.forEach((item) => {
        if (item.textContent.toLowerCase().includes(filter)) {
            item.style.display = "";
            hasResults = true;
        } else {
            item.style.display = "none";
        }
    });

    dropdown.style.display = hasResults ? "block" : "none";
});

// Seleccionar elemento con delegación de eventos
listUl.addEventListener("click", (event) => {
    const inputHiddenCliente = $("#id_cliente_modal");
    const item = event.target.closest("li"); // Detectar solo clicks en <li>
    if (item) {
        inputHiddenCliente.val(item.getAttribute("data-id"));
        searchInputClientes.value = item.textContent;
        dropdown.style.display = "none";
    }
});


/*
este es un ejemplo de uso

<div id="cambiarCliente" class="modal fade top20" role="dialog" aria-labelledby="cambiarCliente"
                        aria-hidden="true" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title text-center">Cambiar cliente</h4>
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">x</button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="recipient-name-tarjeta" class="form-label">Cliente:</label>
                                        <input type="hidden" id="id_cliente_modal">
                                        <div class="form-group position-relative">
                                            <input type="text" id="search_clientes" class="form-control"
                                                placeholder="Buscar..." autocomplete="off" />
                                            <div id="content_clientes"
                                                class="position-absolute w-100 bg-white border rounded"
                                                style="max-height: 150px; overflow-y: auto; display: none;">
                                                <ul id="list_clientes" class="list-group">

                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary waves-effect"
                                        data-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-info waves-effect waves-light"
                                        name="submit">Cambiar</button>
                                </div>
                            </div>
                        </div>
                    </div>
*/