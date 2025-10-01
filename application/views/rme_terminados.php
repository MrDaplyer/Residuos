<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">RME (Terminados) <span class="badge badge-primary">RME</span></h1>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros de Búsqueda</h6>
        </div>
        <div class="card-body">
            <form id="filter-form">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="filtro_residuo">Tipo de Residuo</label>
                            <select id="filtro_residuo" class="form-control selectpicker" multiple data-live-search="true" title="Todos...">
                                <?php foreach ($residuos_rme as $residuo): ?>
                                    <option value="<?= $residuo; ?>"><?= $residuo; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_inicio">Desde</label>
                            <input type="date" id="fecha_inicio" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="fecha_fin">Hasta</label>
                            <input type="date" id="fecha_fin" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group w-100">
                            <button type="button" id="btn_filter" class="btn btn-primary btn-block">Filtrar</button>
                            <button type="button" id="btn_clear_filter" class="btn btn-secondary btn-block mt-2">Limpiar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Datatables -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="width:32px;" class="text-center">
                                <input type="checkbox" id="selectAll-terminados">
                            </th>
                            <th>ID</th>
                            <th style="width:32px;" class="text-center"></th>
                            <th>Trabajador</th>
                            <th>Nombre del Residuo</th>
                            <th>Clave</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Área de Almacenamiento</th>
                            <th>Área de Generación</th>
                            <th>Fecha de Ingreso</th>
                            <th>Fecha de Salida</th>
                            <th>Fase Siguiente</th>
                            <th>Razón Social</th>
                            <th>No. Manifiesto</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    var baseUrl = "<?= base_url() ?>";
</script>