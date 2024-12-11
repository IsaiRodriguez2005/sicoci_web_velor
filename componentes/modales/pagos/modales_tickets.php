<div class="modal fade" id="modal_opciones_series_tickets" role="dialog" style="overflow: scroll;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-success">Series de Tickets</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive" id="series_tickets">
                    <table class="table table-striped" id="tabla_series_tickets" width="100%">
                        <thead>
                            <tr>
                                <th class="sticky text-center">Acciones</th>
                                <th class="sticky text-center">ID</th>
                                <th class="text-center">Documento</th>
                                <th class="text-center">Serie</th>
                                <th class="text-center">Código Postal</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody id="mostrar_series_tickets">

                        </tbody>
                    </table>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="cerrar_modal('modal_opciones_series_tickets', '')">Regresar</button>
            </div>
        </div>
    </div>
</div>
