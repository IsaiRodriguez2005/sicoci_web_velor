<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <?php echo "<span class='nav-link'><b>Bienvenido:</b> ".$_SESSION['nombre_usuario']."</span>"; ?>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Notifications Dropdown Menu -->
      <span class="nav-link text-sm text-right">
        <b>Raz&oacute;n Social:</b><span class="text-danger text-sm"> <?php echo $_SESSION['nombre_comercial']; ?></span> <br>
        <b>Vigencia del CSD:</b><span class="text-danger text-sm"> <?php //echo date("d/m/Y", strtotime($_SESSION['sello'])); ?></span>
      </span>
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-user"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">Mi cuenta de usuario</span>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item" onclick="cerrar_sesion(<?php echo $_SESSION['id_usuario']; ?>);">
            <i class="fas fa-door-open mr-2"></i> Cerrar sesi&oacute;n
          </a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->