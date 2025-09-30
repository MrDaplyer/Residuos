<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <!-- TopBar -->
        <nav class="navbar navbar-expand navbar-light bg-navbar topbar mb-4 static-top">
            <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img class="img-profile rounded-circle" src="<?= base_url('assets/'); ?>img/boy.png" style="max-width: 60px">
                        <span class="ml-2 d-none d-lg-inline text-white small">
                            <?= $this->session->userdata('nombre') ? $this->session->userdata('nombre') : 'Usuario' ?>
                            <?php if ($this->session->userdata('role') === 'admin'): ?>
                                <small class="d-block" style="font-size: 11px; opacity: 0.8;">(Administrador)</small>
                            <?php else: ?>
                                <small class="d-block" style="font-size: 11px; opacity: 0.8;">(Empleado #<?= $this->session->userdata('NumEmpleado') ?>)</small>
                            <?php endif; ?>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                        <div class="dropdown-header">
                            <strong><?= $this->session->userdata('nombre') ?></strong>
                            <br>
                            <small class="text-muted">
                                <?= $this->session->userdata('role') === 'admin' ? 'Administrador' : 'Empleado #' . $this->session->userdata('NumEmpleado') ?>
                            </small>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?= base_url('auth/logout') ?>">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Cerrar Sesión
                        </a>
                    </div>
                </li>
            </ul>
        </nav>
        <!-- Topbar -->