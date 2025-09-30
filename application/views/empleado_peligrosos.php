<div class="container-fluid"
    data-success-message="<?= isset($success_message) ? $success_message : '' ?>"
    data-peligrosos-data='<?= json_encode($peligrosos_data) ?>'>

    <h1 class="h3 mb-4 text-danger font-weight-bold">Residuos Peligrosos</h1>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form action="<?= base_url('empleado/guardar_peligroso'); ?>" method="POST">
                        <div class="form-group">
                            <label for="trabajador">Número de Empleado</label>
                            <input type="text" class="form-control" id="trabajador" name="trabajador" value="<?= $empleado_numero ?>" readonly style="background-color: #f8f9fa;">
                        </div>
                        <div class="form-group">
                            <label for="residuo">Nombre del Residuo</label>
                            <select id="residuo" name="residuo" class="form-control selectpicker" data-live-search="true" data-size="8" title="Elegir..." required>
                                <?php foreach ($peligrosos_data as $nombre => $data): ?>
                                    <option value="<?= $nombre; ?>"><?= $nombre; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="cantidad">Cantidad</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="cantidad" name="cantidad" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="unidad">Unidad</label>
                                <input type="text" class="form-control" id="unidad" name="unidad" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="crp">Código de Peligrosidad (CRP)</label>
                            <input type="text" class="form-control" id="crp" name="crp" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="area_generacion">Área de Generación</label>
                            <select id="area_generacion" name="area_generacion" class="form-control selectpicker" data-live-search="true" data-size="8" title="Elegir..." required>
                                <?php foreach ($areas_generacion as $area): ?>
                                    <option value="<?= $area; ?>"><?= $area; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ingreso">Fecha de Ingreso</label>
                            <input type="date" class="form-control" id="ingreso" name="ingreso" readonly>
                        </div>
                        <button type="submit" class="btn btn-danger">Guardar Registro</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>