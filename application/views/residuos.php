<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <!-- Tabla RME -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h4 class="m-0 font-weight-bold text-primary">RME</h4>
                    <button class="btn btn-primary btn-sm" onclick="add_residuo()">
                        <i class="fas fa-plus"></i> Agregar
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="table_rme" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Clave</th>
                                    <th>Unidad</th>
                                    <th>Almacén</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla Residuos Peligrosos -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h4 class="m-0 font-weight-bold text-danger">Residuos Peligrosos</h4>
                    <button class="btn btn-danger btn-sm" onclick="add_peligroso()">
                        <i class="fas fa-plus"></i> Agregar
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="table_peligrosos" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Unidad</th>
                                    <th>CRP</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para RME -->
<div class="modal fade" id="modalRME" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRMETitle">Agregar Residuo RME</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formRME">
                <div class="modal-body">
                    <input type="hidden" id="rme_id" name="id">
                    <div class="form-group">
                        <label for="rme_nombre">Nombre del Residuo</label>
                        <input type="text" class="form-control" id="rme_nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="rme_clave">Clave</label>
                        <input type="text" class="form-control" id="rme_clave" name="clave" required>
                    </div>
                    <div class="form-group">
                        <label for="rme_unidad">Unidad</label>
                        <input type="text" class="form-control" id="rme_unidad" name="unidad" required>
                    </div>
                    <div class="form-group">
                        <label for="rme_almacen">Almacén</label>
                        <input type="text" class="form-control" id="rme_almacen" name="almacen" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Residuos Peligrosos -->
<div class="modal fade" id="modalPeligroso" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPeligrosoTitle">Agregar Residuo Peligroso</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formPeligroso">
                <div class="modal-body">
                    <input type="hidden" id="peligroso_id" name="id">
                    <div class="form-group">
                        <label for="peligroso_nombre">Nombre del Residuo</label>
                        <input type="text" class="form-control" id="peligroso_nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="peligroso_unidad">Unidad</label>
                        <input type="text" class="form-control" id="peligroso_unidad" name="unidad" required>
                    </div>
                    <div class="form-group">
                        <label>CRP</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="crp_te" value="Te">
                                    <label class="form-check-label" for="crp_te">
                                        <strong>Te</strong> - Tóxico
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="crp_i" value="I">
                                    <label class="form-check-label" for="crp_i">
                                        <strong>I</strong> - Inflamable
                                    </label>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="peligroso_crp" name="crp">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>