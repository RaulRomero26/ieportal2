<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo  base_url; ?>public/css/bootstrap/bootstrap.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url ?>public/css/general/root.css">
    <!-- <link rel='shortcut icon' type='image/ico' href='<?php echo base_url ?>public/media/icons/favicon.ico' /> -->
    <?php if (isset($data['extra_css']))  echo $data['extra_css'] ?>
    <link rel="icon" type="image/png" href="<?php echo base_url ?>public/media/icons/icon.png"/>
    <title><?php echo $data['titulo'] ?></title>
</head>

<body>
    <nav class="navbar" id="nav_login"></nav>
    <div class="container abs-center">
        <div class="container-center-backgroud shadow p-5">
            <div class="row form">
                <div class=" col-lg-12 mt-n3">
                    <div class="text-center">
                        <img src="<?php echo base_url; ?>public/media/images/ielogo.png" class="mt-4 mb-3 logo" >
                        <h5 class="card-title mb-5 mt-3 col-12">Sistema de Administración IE</h5>
                        <form action="<?= base_url;?>Login/login" method="POST" class="needs-validation" novalidate autocomplete="off">
                            <div class="form-row  mb-5 container-fluid">
                                <div class="col-md-12 col-lg-5 mb-lg-3 mb-md-1 mt-lg-1">
                                    <label for="usuario">Usuario:</label>
                                </div>

                                <div class="col-md-12 col-lg-7 mt-lg-1">
                                    <input name="User_Name" type="text" class="form-control" id="usuario" placeholder="Ingrese su usuario" required value="<?php echo(isset($data['post']['User_Name']))? $data['post']['User_Name']:"";?>">
                                    <div class="invalid-feedback">Este campo es obligatorio</div>
                                </div>

                                <div class="col-md-12 col-lg-5 mb-lg-3 mb-md-1 mt-lg-4">
                                    <label for="contrasena">Contraseña:</label>
                                </div>

                                <div class="col-md-12 col-lg-7 mt-lg-4">
                                    <input name="Password" type="password" class="form-control" id="contrasena" placeholder="Ingrese su contraseña" required>
                                    <div class="invalid-feedback">Este campo es obligatorio</div>
                                </div>
                            </div>
                           
                            <div class=" mb-5" >
                                <span style="color: red;"><?php echo (isset($data['ErrorMessage']))?$data['ErrorMessage']:"";?></span>
                            </div>
                            

                            <div class="form-check mt-n5">
                                <input class="form-check-input" type="checkbox" value="" id="check_pass">
                                <label class="form-check-label" for="check_pass">
                                    Mostrar contraseña
                                </label>
                            </div>
                            <button class="btn btn-ssc mt-4" type="submit" name="enviarLogin">Entrar</button>
                        </form>

                        <!-- <img src="<?php echo base_url; ?>public/media/images/logo_transito.png" alt="" width="100px" class="mt-4"> -->
                    </div>
                </div>
            </div>

        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="<?php echo base_url; ?>public/js/bootstrap/bootstrap.js" ></script>
    <?php if (isset($data['extra_js']))  echo $data['extra_js']; ?>

    <!-- <footer>
        <div class="footer-text text-center text-muted">© 2021 Copyright:
            ©2021 Todos los derechos reservados. SSCMP
        </div>
    </footer> -->

</body>

</html>