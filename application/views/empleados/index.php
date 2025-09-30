<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestión de Empleados</h1>
        <button class="btn btn-primary" onclick="nuevoEmpleado()">
            <i class="fas fa-plus mr-2"></i>Nuevo Empleado
        </button>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Empleados</h6>
        </div>
        <div class="table-responsive p-3">
            <table class="table align-items-center table-flush" id="empleadosTable" width="100%">
                <thead class="thead-light">
                    <tr>
                        <th style="display: none;">ID</th>
                        <th>Número</th>
                        <th>Nombre</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th style="display: none;">ID</th>
                        <th>Número</th>
                        <th>Nombre</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </tfoot>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal para Crear/Editar Empleado -->
<div class="modal fade" id="empleadoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="empleadoModalLabel">Nuevo Empleado</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="empleadoForm">
                <div class="modal-body">
                    <input type="hidden" id="empleado_id" name="empleado_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="NumEmpleado">Número de Empleado</label>
                                <input type="text" class="form-control" id="NumEmpleado" name="NumEmpleado" required 
                                       placeholder="Ej: 26, 170, Aux1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre">Nombre Completo</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required 
                                       placeholder="Nombre completo del empleado">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="es_admin" name="es_admin">
                            <label class="custom-control-label" for="es_admin">
                                Es Administrador?
                            </label>
                            <small class="form-text text-muted">
                                Si esta marcado, el usuario tendra acceso completo al sistema
                            </small>
                        </div>
                    </div>

                    <div class="form-group" id="contrasenaGroup" style="display: none;">
                        <label for="contrasena">Contraseña (Solo para Administradores)</label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" 
                               placeholder="Contraseña para acceso de administrador">
                        <small class="form-text text-muted">
                            Los empleados regulares no requieren contraseña
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Empleado</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var empleadosAjaxUrl = "<?= base_url('empleados/get_empleados_ajax') ?>";
    var baseUrl = "<?= base_url() ?>";
</script>