<?php
session_start();

if (isset($_SESSION['id_usuario'])) {
  header('Location: sistema.php');
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php include("componentes/estructura/title.php"); ?></title>
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="icon" type="image/icon" href="favicon.ico" />
  <!-- Sweet Alerts-->
  <script src="js/sweetalert2@11.js"></script>
  <!-- Funciones JS Personalizadas -->
  <script src="js/peticiones_login.js"></script>
</head>

<body class="hold-transition login-page" style="background-image: url('img/login/erp.jpg'); background-size: cover;">
  <div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <img src="img/logo.png">
      </div>
      <div class="card-body">
        <p class="login-box-msg">Ingresa tu informaci&oacute;n para iniciar sesi&oacute;n</p>
        <div class="input-group mb-3">
          <input type="email" class="form-control" placeholder="Correo electr&oacute;nico" id="correo" name="correo" onfocus="resetear('correo')">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Contrase&ntilde;a" id="pass" name="pass" onfocus="resetear('pass')">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="PIN" id="pin" name="pin" onfocus="resetear('pin')" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-fingerprint"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block" onclick="validar_login();">Iniciar Sesi&oacute;n</button>
          </div>
          <!-- /.col -->
        </div>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.login-box -->

  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.min.js"></script>

</body>

</html>