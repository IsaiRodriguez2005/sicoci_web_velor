// Controlar los modales en pila
$(document).on('show.bs.modal', '.modal', function () {
    // Contar cuántos modales están abiertos
    const openModals = $('.modal.show').length;

    if (openModals > 0) {
        // Ajustar el z-index del nuevo modal
        $(this).css('z-index', 1040 + (10 * openModals));

        // Ajustar el z-index del backdrop
        $('.modal-backdrop').not('.modal-stack')
            .css('z-index', 1039 + (10 * openModals))
            .addClass('modal-stack');
    }
});

$(document).on('hidden.bs.modal', '.modal', function () {
    // Cuando se cierra un modal, eliminar backdrop si ya no hay modales abiertos
    if ($('.modal.show').length === 0) {
        $('.modal-backdrop').removeClass('modal-stack');
    }
});
