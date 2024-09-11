<?php
    session_start();
    //require("../conexion.php");

    if(empty($_SESSION['id_usuario']) || empty($_SESSION['nombre_usuario']))
    {
        session_destroy();
        echo "
            <script>
                window.location = 'index.html';
            </script>
        ";
    }
    else
    {
        ?>
        <!-- Mosaico Solicitadas -->
            <div class="col-lg-3 col-6">
              <a href="#" class="small-box-footer" onclick="mostrar_emisores_inactivos();">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <?php
                          $sqlInactivos = "SELECT COUNT(*) AS contar FROM emisores WHERE estatus = 2";
                          $resInactivos = mysqli_query($conexion, $sqlInactivos);
                          $inactivos = mysqli_fetch_array($resInactivos);
                          echo "<h3>".$inactivos['contar']."</h3>";
                        ?>
                        <p>Emisores inactivos</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-alert"></i>
                    </div>
                </div>
              </a>
            </div>
            <!-- Mosaico Solicitadas -->
            <!-- Mosaico En Proceso -->
            <div class="col-lg-3 col-6">
              <a href="#" onclick="mostrar_stock_timbres();" class="small-box-footer">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <?php
                          $sqlStock = "SELECT COUNT(*) AS stock_timbres FROM emisores WHERE timbres < 100 AND estatus = 1";
                          $resStock = mysqli_query($conexion, $sqlStock);
                          $stock = mysqli_fetch_array($resStock);
                          echo "<h3>".$stock['stock_timbres']."</h3>";
                        ?>
                        <p>Stock Minimo Timbres</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-gear-b"></i>
                    </div>
                </div>
              </a>
            </div>
            <!-- Mosaico En Proceso -->
            <!-- Modal Stock -->
            <div class="modal fade" id="emisores_stock">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h4 class="modal-title">Emisores con stock de timbres al m&iacute;nimo</h4>
                        </div>
                        <div class="modal-body">
                            <div id="mostrar_emisores_stock">
                            </div>
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- Modal Stock -->
            <!-- Modal Inactivos -->
            <div class="modal fade" id="emisores_inactivos">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h4 class="modal-title">Emisores inactivos</h4>
                        </div>
                        <div class="modal-body">
                            <div id="mostrar_emisores_inactivos">
                            </div>
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- Modal Inactivos -->
        <?php
    }
?>
