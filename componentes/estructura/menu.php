<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
        <img src="img/logo-small.png" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light"> COSERA</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="sistema.php" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Panel</p>
                    </a>
                </li>
                <?php
                if ($_SESSION['id_emisor'] == 0) {
                ?>
                    <li class="nav-item">
                        <a href="emisores.php" class="nav-link">
                            <i class="nav-icon fas fa-industry"></i>
                            <p>Emisores</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="asignar_modulos.php" class="nav-link">
                            <i class="nav-icon fas fa-exchange-alt"></i>
                            <p>Emisor - Modulo</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="usuarios.php" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Usuarios</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="timbres.php" class="nav-link">
                            <i class="nav-icon fas fa-bell"></i>
                            <p>Timbres</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="documentos.php" class="nav-link">
                            <i class="nav-icon fas fa-file"></i>
                            <p>Documentos fiscales</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="modulos.php" class="nav-link">
                            <i class="nav-icon fas fa-brain"></i>
                            <p>Modulos</p>
                        </a>
                    </li>
                    <?php
                } else {
                    if (empty($_SESSION['id_personal'])) {
                    ?>
                        <li class="nav-item">
                            <a href="configuraciones.php" class="nav-link">
                                <i class="nav-icon fas fa-cog"></i>
                                <p>Configuraciones</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="agenda.php" class="nav-link">
                                <i class="nav-icon fas fa-calendar-day"></i>
                                <p>Agenda</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-address-book"></i>
                                <p>
                                    C&aacute;talogos
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="clientes.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Clientes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="usuarios.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Usuarios</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="servicios.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Mis Productos/Servicios</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="proveedores.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Proveedores</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="personal.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Personal</p>
                                    </a>
                                </li>
                                <!--
                                <li class="nav-item">
                                    <a href="convenios.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Convenios</p>
                                    </a>
                                </li>
                                -->
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-cash-register"></i>
                                <p>
                                    Ventas
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="tickets.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Tickets</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="facturas.php" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Facturas</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="pagos.php" class="nav-link">
                                <i class="nav-icon fas fa-coins"></i>
                                <p>Pago proveedores</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="reportes.php" class="nav-link">
                                <i class="nav-icon fas fa-table"></i>
                                <p>Reportes</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="dashboard_directivo.php" class="nav-link">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>Dashboard directivo</p>
                            </a>
                        </li>
                    <?php
                    } else {

                    ?>
                        <li class="nav-item">
                            <a href="agenda.php" class="nav-link">
                                <i class="nav-icon fas fa-calendar-day"></i>
                                <p>Agenda</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="expedientes.php" class="nav-link">
                                <i class="fas fa-folder-open"></i>
                                <p>Expedienes</p>
                            </a>
                        </li>

                <?php
                    }
                }
                ?>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>