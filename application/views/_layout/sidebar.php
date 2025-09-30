<?php 
$role = $this->session->userdata('role'); 

// Solo verificar lotes pendientes para administradores
$num_lotes_rme = 0;
$num_lotes_peligrosos = 0;

if ($role === 'admin' || $role == 1) {
    $CI =& get_instance();
    $CI->load->model('Rme_model');
    $CI->load->model('Residuos_peligrosos_model');
    
    $num_lotes_rme = $CI->Rme_model->contar_lotes_pendientes();
    $num_lotes_peligrosos = $CI->Residuos_peligrosos_model->contar_lotes_pendientes();
}
?>

<!-- Sidebar -->
<ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url() ?>">
        <div class="sidebar-brand-icon">
            <img src="<?= base_url('assets/'); ?>img/logo/logo2.png">
        </div>
        <div class="sidebar-brand-text mx-3" style="color: #007E95;">Erich Jaeger</div>
    </a>
    
    <!-- Divider -->
    <hr class="sidebar-divider">
    
    <?php if ($role === 'admin' || $role == 1): ?>
        <!-- Heading -->
        <div class="sidebar-heading">
            Terminados
        </div>

        <!-- RME Terminados -->
        <li class="nav-item active">
            <a class="nav-link" href="<?= base_url('rme/terminados') ?>">
                <i class="fas fa-fw fa-check-double"></i>
                <span><b>RME Terminados</b></span></a>
        </li>

        <!-- RP Terminados -->
        <li class="nav-item active">
            <a class="nav-link" href="<?= base_url('residuos_peligrosos/terminados') ?>">
                <i class="fas fa-fw fa-check-double"></i>
                <span><b>RP Terminados</b></span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">
        <div class="sidebar-heading">
            Gestión de Residuos
        </div>
        <!-- RME -->
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('rme') ?>">
                <i class="fas fa-fw fa-recycle"></i>
                <span>
                    <?php if ($num_lotes_rme > 0): ?>
                        <span class="badge badge-danger" style="font-size: 12px; margin-right: 5px; padding: 3px 6px;"><?= $num_lotes_rme ?></span>
                    <?php endif; ?>
                    RME
                </span>
            </a>
        </li>

        <!--  Residuos Peligrosos -->
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('residuos_peligrosos') ?>">
                <i class="fas fa-fw fa-biohazard"></i>
                <span>
                    <?php if ($num_lotes_peligrosos > 0): ?>
                        <span class="badge badge-danger" style="font-size: 12px; margin-right: 5px; padding: 3px 6px;"><?= $num_lotes_peligrosos ?></span>
                    <?php endif; ?>
                    Residuos Peligrosos
                </span>
            </a>
        </li>

        <!-- Nueva sección para páginas adicionales -->
        <hr class="sidebar-divider">
        <div class="sidebar-heading">
            Otras Páginas
        </div>
        <!-- Residuos -->
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('residuos') ?>">
                <i class="fas fa-fw fa-trash-alt"></i>
                <span>Residuos</span></a>
        </li>
        <!-- Áreas de Generación -->
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('areas_generacion') ?>">
                <i class="fas fa-fw fa-industry"></i>
                <span>Áreas de Generación</span></a>
        </li>

        <!-- Gestión de Empleados -->
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('empleados') ?>">
                <i class="fas fa-fw fa-users"></i>
                <span>Gestión de Empleados</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">
    <?php elseif ($role === 'empleado' || $role == 2): ?>
        <!-- Heading -->
        <div class="sidebar-heading">
            Portal Empleado
        </div>

        <!-- Seleccionar Tipo de Residuo -->
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('empleado/dashboard') ?>">
                <i class="fas fa-fw fa-home"></i>
                <span>Inicio</span></a>
        </li>

        <!-- RME -->
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('empleado/rme') ?>">
                <i class="fas fa-fw fa-recycle"></i>
                <span>Registrar RME</span></a>
        </li>

        <!-- Residuos Peligrosos -->
        <li class="nav-item">
            <a class="nav-link" href="<?= base_url('empleado/peligrosos') ?>">
                <i class="fas fa-fw fa-biohazard"></i>
                <span>Registrar Peligrosos</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">
    <?php endif; ?>
    
    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

</ul>
<!-- Sidebar -->