<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="<?= base_url('assets/'); ?>img/logo/favicon.ico" rel="icon">
    <title>Erich Jaeger - Login</title>
    <link href="<?php echo base_url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/vendor/fontawesome-free/css/all.min.css') ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url('assets/css/ruang-admin.min.css') ?>" rel="stylesheet">
    <style>
        body { background-color: #f8f9fc; }
        .login-card { max-width: 400px; margin: 100px auto; }
        .login-logo { font-size: 2rem; color: #5a5c69; }
        .btn-login { background-color: #6777ef; border-color: #6777ef; }
        .toggle-buttons {
            display: flex;
            margin-bottom: 20px;
            border-radius: 5px;
            overflow: hidden;
            border: 1px solid #d1d3e2;
        }
        .toggle-btn {
            flex: 1;
            padding: 10px 15px;
            border: none;
            background: #f8f9fc;
            color: #5a5c69;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .toggle-btn.active {
            background: #6777ef;
            color: white;
        }
        .toggle-btn:hover:not(.active) {
            background: #eaecf4;
        }
        .login-type-text {
            text-align: center;
            margin-bottom: 15px;
            font-size: 14px;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-card">
            <div class="card shadow">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <img src="<?php echo base_url('assets/img/logo/EJLogo.png') ?>" alt="Logo" class="mb-2" style="width: 100px;">
                        <h5 class="login-logo">Residuos</h5>
                    </div>

                    <!-- Toggle Buttons -->
                    <div class="toggle-buttons">
                        <button type="button" class="toggle-btn active" id="adminToggle">
                            <i class="fas fa-user-shield mr-1"></i>ADMINISTRADOR
                        </button>
                        <button type="button" class="toggle-btn" id="empleadoToggle">
                            <i class="fas fa-user mr-1"></i>EMPLEADO
                        </button>
                    </div>

                    <!-- la indicacoin del tipo de login -->
                    <div class="login-type-text" id="loginTypeText">
                        Acceso de Administrador
                    </div>

                    <!-- Mensajes de Error -->
                    <?php if ($this->session->flashdata('error')): ?>
                        <div class="alert alert-danger mb-4">
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Formulario de Login -->
                    <form method="post" action="<?php echo base_url('auth/login') ?>" id="loginForm">
                        <input type="hidden" name="login_type" id="loginType" value="admin">
                        
                        <!-- Campo Usuario/Número Empleado -->
                        <div class="form-group">
                            <input type="text" class="form-control form-control-user" id="usuario" name="usuario" placeholder="Usuario" required>
                        </div>
                        
                        <!-- Campo Contraseña (solo para admin) -->
                        <div class="form-group" id="passwordGroup">
                            <input type="password" class="form-control form-control-user" id="contrasena" name="contrasena" placeholder="Contraseña" required>
                        </div>
                        
                        <!-- Botón de Login -->
                        <button type="submit" class="btn btn-login btn-user btn-block text-white">INICIAR SESIÓN</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?php echo base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    
    <script>
        $(document).ready(function() {
            // Toggle entre Admin y Empleado
            $('#adminToggle').click(function() {
                // Activar botón admin
                $('.toggle-btn').removeClass('active');
                $(this).addClass('active');
                
                // Cambiar texto indicador
                $('#loginTypeText').text('Acceso de Administrador');
                
                // Mostrar campo contraseña
                $('#passwordGroup').show();
                $('#contrasena').attr('required', true);
                
                // Cambiar placeholder
                $('#usuario').attr('placeholder', 'Usuario');
                
                // Cambiar tipo de login
                $('#loginType').val('admin');
            });
            
            $('#empleadoToggle').click(function() {
                // Activar botón empleado
                $('.toggle-btn').removeClass('active');
                $(this).addClass('active');
                
                // Cambiar texto indicador
                $('#loginTypeText').text('Acceso de Empleado');
                
                // Ocultar campo contraseña
                $('#passwordGroup').hide();
                $('#contrasena').removeAttr('required');
                
                // Cambiar placeholder
                $('#usuario').attr('placeholder', 'Número de Empleado');
                
                // Cambiar tipo de login
                $('#loginType').val('empleado');
            });
        });
    </script>
</body>

</html>