<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    

    <title><?= $data['titulo'] ?></title>

    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="<?php echo base_url ?>public/css/bootstrap/bootstrap.css">
    <!-- ----- ----- ----- Root CSS ----- ----- ----- -->
    <link rel="stylesheet" href="<?php echo base_url ?>public/css/general/root.css">
    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="<?php echo base_url ?>public/css/template/style.css">
    <!-- Scrollbar Custom CSS -->
    <link rel="stylesheet" href="<?php echo base_url ?>public/css/template/header/customScrollbar.min.css">
    <!--Material Icons-->
    <link rel="stylesheet" href="<?= base_url ?>public/media/icons/material_design_icons/material-icons.css">
    <!--link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"-->
    <!-- <link rel='shortcut icon' type='image/ico' href='<?php echo base_url ?>public/media/icons/favicon.ico' /> -->
    <!--link rel="stylesheet" href="<?= base_url ?>public/media/icons/bootstrap_icons/bootstrap-icons.css"-->
    <script src= '<?php echo base_url ?>public/js/libraries/autocomplete.js'></script>
  <link rel="stylesheet" type="text/css" href='<?php echo base_url ?>public/css/libraries/autocomplete.js'>
    
    <meta name="theme-color" content="#6D0725" />


    <!-- Font Awesome JS -->
    <script defer src="<?php echo base_url ?>public/js/template/header/solid.js"></script>
    <script defer src="<?php echo base_url ?>public/js/template/header/fontawesome.js"></script>

    <?php if (isset($data['extra_css']))  echo $data['extra_css'] ?>
    <?php if (isset($data['extra'])) echo $data['extra'] ?>

  </head>



  <body>
    <!-- Sidebar  -->
    <nav id="sidebar">
      <div id="dismiss">
        <i class="fas fa-arrow-left"></i>
      </div>
      <div class="sidebar-header mt-4 text-center">
        <h3>IE Portal</h3>
      </div>
      <ul class="list-unstyled ">
        <li class="container mt-3">
          <div class="text-divider">
            <i class="material-icons">home</i>
          </div>
        </li>
        <li class="container">
          <a href="<?= base_url ?>Inicio">Inicio</a>
        </li>
        <li class="container mt-3">
          <div class="text-divider">
            <i class="material-icons">desktop_windows</i>
          </div>
        </li>
        <?php
        if ($_SESSION['userdata']->Modo_Admin == 1  || $_SESSION['userdata']->Clases[2]) {
        ?>
        <li class="container">
          <a href="<?= base_url;?>Clases">Clases</a>
        </li>
  
        <?php
        }
        ?>
        
        
        
        
        <?php
        if ($_SESSION['userdata']->Modo_Admin == 1) {
        ?>
          <li class="container mt-3">
            <div class="text-divider">
              <i class="material-icons">admin_panel_settings</i>
            </div>
          </li>
          <li class="container">
            <a href="<?= base_url; ?>Catalogos">Catálogos</a>
          </li>
          <li class="container">
            <a href="<?= base_url; ?>UsersAdmin/">Usuarios</a>
          </li>
          <li class="container">
            <a href="<?= base_url; ?>Historiales">Historial</a>
          </li>
          <li class="container">
            <a href="<?= base_url; ?>Pagos">Pagos</a>
          </li>
        <?php
        }
        ?>
        <li class="container mt-3">
          <div class="text-divider">
            <i class="material-icons">account_box</i>
          </div>
        </li>
      </ul>
      <ul class="list-unstyled CTAs">
        <li class="container">
          <a href="<?= base_url; ?>Cuenta" class="download btn-ssc">Mi perfil</a>
        </li>
        <li class="container">
          <a href="<?= base_url; ?>Login/logOut" class="download">Cerrar sesión</a>
        </li>
      </ul>
    </nav>
    <!-- Page Content  -->
    <div id="content">
      <div class="container">
        <div class="row">
          <div class="col-sm-6 pr-0 d-flex justify-content-center align-items-center">
            <!-- <img src="<?php echo base_url; ?>public/media/images/estrella-c.png" width="10%" class="my-1"> -->
          </div>
        </div>
      </div>
      <nav class="navbar sticky-top" id="sidenav_p">
        <div class="container">
          <img class="navbar-brand" src="<?php echo base_url; ?>public/media/icons/menu.svg" alt="" id="sidebarCollapse" width="24px">
          <div class="dropdown">
            <button class="btn btn-nav dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Bienvenida(o) <?= $_SESSION['userdata']->Nombre;?>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <a class="dropdown-item" href="<?= base_url; ?>Cuenta">Mi perfil</a>
              <a class="dropdown-item" href="<?= base_url; ?>Login/logOut">Cerrar sesión</a>
            </div>
          </div>
        </div>
      </nav>
      <div class="">
      <div class="modal fade" id="modalLogin" tabindex="-1" aria-labelledby="modalLoginLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-body">
              <div class="text-center">
                <img src="<?php echo base_url; ?>public/media/images/banner.png" alt="" width="350px" height="80px" class="mt-4 mb-3">
                <h5 class="card-title mb-5 mt-3">SARAI</h5>
                <form id="data_login_modal" onsubmit="event.preventDefault();">
                  <div class="form-row  mb-5 container-fluid">
                      <div class="col-md-12 col-lg-5 mb-lg-3 mb-md-1 mt-lg-1">
                          <label for="usuario">Usuario:</label>
                      </div>

                      <div class="col-md-12 col-lg-7 mt-lg-1">
                          <input name="User_Name" type="text" class="form-control" id="usuario" placeholder="Ingrese su usuario" required>
                          <span class="span_error" id="error_usuario_login"></span>
                      </div>

                      <div class="col-md-12 col-lg-5 mb-lg-3 mb-md-1 mt-lg-4">
                          <label for="contrasena">Contraseña:</label>
                      </div>

                      <div class="col-md-12 col-lg-7 mt-lg-4">
                          <input name="Password" type="password" class="form-control" id="contrasena" placeholder="Ingrese su contraseña" required>
                          <span class="span_error" id="error_contrasena_login"></span>
                      </div>
                  </div>
                    
                  <div class=" mb-5" >
                      <span style="color: red;" id="error_login_feedback"></span>
                  </div>
                  <div class="form-check mt-n5">
                      <input class="form-check-input" type="checkbox" value="" id="check_pass">
                      <label class="form-check-label" for="check_pass">
                          Mostrar contraseña
                      </label>
                  </div>
                  <button class="btn btn-ssc mt-4" type="button" id="btn_login_fetch">Entrar</button>
                </form>
                <img src="<?php echo base_url; ?>public/media/images/logo_transito.png" alt="" width="100px" class="mt-4">
              </div>
            </div>
          </div>
        </div>
      </div>