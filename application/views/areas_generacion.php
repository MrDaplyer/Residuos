<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Áreas de Generación</h6>
                    <button class="btn btn-primary btn-sm" onclick="add_area()">
                        <i class="fas fa-plus"></i> Agregar Área
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="table_areas" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Estado</th>
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

<!-- Modal para Áreas de Generación -->
<div class="modal fade" id="modalArea" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAreaTitle">Agregar Área de Generación</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formArea">
                <div class="modal-body">
                    <input type="hidden" id="area_id" name="id">
                    <div class="form-group">
                        <label for="area_nombre">Nombre del Área</label>
                        <input type="text" class="form-control" id="area_nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="area_descripcion">Descripción</label>
                        <textarea class="form-control" id="area_descripcion" name="descripcion" rows="3" placeholder="Se puede dejar en blanco"></textarea>
                    </div>
                    <div class="form-group" id="estado_group" style="display: none;">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="area_activo" name="activo" checked>
                            <label class="form-check-label" for="area_activo">
                                Área activa
                            </label>
                        </div>
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