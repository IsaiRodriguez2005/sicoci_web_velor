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

<div class="modal fade" id="modal_orden_compra_ticket" role="dialog" style="overflow: scroll;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-success">Series de Tickets</h4>
                <input type="hidden" id="idSerieTicket" value="0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="card-body">
                <div class="callout callout-info">
                    <div class="row">
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-store"></i></span>
                                </div>
                                <div class="search-container">
                                    <input type="hidden" id="id_producto">
                                    <input type="text" id="search" placeholder="Buscar..." oninput="filtrar_lista()" />
                                    <ul id="suggestions" class="suggestions hidden">

                                    </ul>
                                </div>
                                <!-- <select class="form-control text-sm" id="articulos_servicios">
                                    <option value="0">Selecciona concepto...</option>
                                    <option value="4">[43231500] IMPLEMENTACION PERSONALIZADA</option>
                                    <option value="2">[43231500] LICENCIA ANUAL SIAT PRO</option>
                                </select> -->
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                </div>
                                <input type="number" class="form-control text-sm" id="cantidad" placeholder="Cantidad" onfocus="resetear(&quot;cantidad&quot;)">
                                <input type="hidden" id="e_partida" value="0">
                                <input type="hidden" id="clave_sat_concepto" value="43231500">
                                <input type="hidden" id="clave_sat_medida" value="E48">
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                </div>
                                <input type="number" class="form-control text-sm" id="precio" placeholder="Precio" onfocus="resetear(&quot;precio&quot;)">
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                </div>
                                <input type="number" class="form-control text-sm" id="iva" placeholder="IVA" onfocus="resetear(&quot;iva&quot;)">
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                </div>
                                <input type="number" class="form-control text-sm" id="retencion" placeholder="Retención" onfocus="resetear(&quot;retencion&quot;)">
                            </div>
                        </div>
                        <div class="4">
                            <div class="input-group">
                                <div class="icheck-success d-inline">
                                    <input type="checkbox" id="iva_exento" onclick="validar_iva_exento()">
                                    <label for="iva_exento">
                                        Exento de IVA
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-list"></i></span>
                                </div>
                                <textarea class="form-control text-sm" id="descripcion_concepto" placeholder="Descripción del concepto" onfocus="resetear(&quot;descripcion_concepto&quot;)" onkeypress="return check_caracter(event)"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <center><button type="button" class="btn btn-success text-sm" onclick="agregar_producto();"><i class="fas fa-plus"></i> Agregar concepto</button></center><br>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12" id="tabla_productos">
                            <div id="tabla_productos2_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                <div class="row">
                                    <div class="col-sm-12 col-md-6"></div>
                                    <div class="col-sm-12 col-md-6">
                                        <div id="tabla_productos2_filter" class="dataTables_filter"><label>Buscar:<input type="search" class="form-control form-control-sm" placeholder="" aria-controls="tabla_productos2"></label></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-striped dataTable no-footer dtr-inline" id="tabla_productos2" aria-describedby="tabla_productos2_info">
                                            <thead>
                                                <tr>
                                                    <th class="sticky text-center text-sm sorting sorting_asc" tabindex="0" aria-controls="tabla_productos2" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Acciones: Activar para ordenar la columna de manera descendente">Acciones</th>
                                                    <th class="sticky text-center text-sm sorting" tabindex="0" aria-controls="tabla_productos2" rowspan="1" colspan="1" aria-label="Partida: Activar para ordenar la columna de manera ascendente">Partida</th>
                                                    <th class="text-center text-sm sorting" tabindex="0" aria-controls="tabla_productos2" rowspan="1" colspan="1" aria-label="Cantidad: Activar para ordenar la columna de manera ascendente">Cantidad</th>
                                                    <th class="text-center text-sm sorting" tabindex="0" aria-controls="tabla_productos2" rowspan="1" colspan="1" aria-label="Descripción: Activar para ordenar la columna de manera ascendente">Descripción</th>
                                                    <th class="text-center text-sm sorting" tabindex="0" aria-controls="tabla_productos2" rowspan="1" colspan="1" aria-label="P/U: Activar para ordenar la columna de manera ascendente">P/U</th>
                                                    <th class="text-center text-sm sorting" tabindex="0" aria-controls="tabla_productos2" rowspan="1" colspan="1" aria-label="IVA: Activar para ordenar la columna de manera ascendente">IVA</th>
                                                    <th class="text-center text-sm sorting" tabindex="0" aria-controls="tabla_productos2" rowspan="1" colspan="1" aria-label="Retención IVA: Activar para ordenar la columna de manera ascendente">Retención IVA</th>
                                                    <th class="text-center text-sm sorting" tabindex="0" aria-controls="tabla_productos2" rowspan="1" colspan="1" aria-label="Importe: Activar para ordenar la columna de manera ascendente">Importe</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tabla_rows">

                                                <tr class="odd">
                                                    <td valign="top" colspan="8" class="dataTables_empty">Ningún dato disponible en esta tabla</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-md-5">
                                        <div class="dataTables_info" id="tabla_productos2_info" role="status" aria-live="polite">Mostrando registros del 0 al 0 de un total de 0 registros</div>
                                    </div>
                                    <div class="col-sm-12 col-md-7">
                                        <div class="dataTables_paginate paging_simple_numbers" id="tabla_productos2_paginate">
                                            <ul class="pagination">
                                                <li class="paginate_button page-item previous disabled" id="tabla_productos2_previous"><a href="#" aria-controls="tabla_productos2" data-dt-idx="0" tabindex="0" class="page-link">Anterior</a></li>
                                                <li class="paginate_button page-item next disabled" id="tabla_productos2_next"><a href="#" aria-controls="tabla_productos2" data-dt-idx="1" tabindex="0" class="page-link">Siguiente</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="cerrar_modal('modal_opciones_series_tickets', '')">Regresar</button>
            </div>
        </div>
    </div>
</div>
<script>

</script>