<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Lotes de Residuos Peligrosos Pendientes de Salida <span class="badge badge-danger">Peligrosos</span></h1>
        <div class="btn-group" role="group" aria-label="acciones-lote-personalizado-peligrosos">
            <button id="btn-finalizar-lote-personalizado-p" class="btn btn-outline-danger mr-2" disabled>
                <i class="fas fa-check-double"></i> Finalizar lote personalizado
                <span class="badge badge-pill badge-danger ml-2" id="badge-seleccionados-p">0</span>
            </button>
            <button id="btn-limpiar-seleccion-p" class="btn btn-light mr-3" style="display:none;">
                <i class="fas fa-eraser"></i>
            </button>
            <button id="btn-lote-personalizado-peligrosos" class="btn btn-danger btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-layer-group"></i>
                </span>
                <span class="text">Lote Personalizado</span>
            </button>
        
        <style>
        /* Animación sutil para tarjetas de lote cuando el modo personalizado está activo */
        .lote-card {
          transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .personalizado-on .lote-card:hover {
          transform: translateY(-4px);
          box-shadow: 0 0.75rem 1.25rem rgba(0,0,0,.15);
        }
        .personalizado-on .lote-card.border-warning {
          border-width: 2px !important;
        }
        </style>
        </div>
    </div>

    <div class="row">
        <?php foreach ($fechas_pendientes as $fecha_data): ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2 lote-card" data-fecha=<?= '"' . $fecha_data['fecha_ingreso_unica'] . '"'; ?> style="cursor: pointer;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Lote de Ingreso</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= date("d/m/Y", strtotime($fecha_data['fecha_ingreso_unica'])); ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                         <button class="btn btn-danger btn-icon-split btn-sm mt-3 btn-completar-lote" data-fecha="<?= $fecha_data['fecha_ingreso_unica']; ?>" onclick="event.stopPropagation();">
                            <span class="icon text-white-50">
                                <i class="fas fa-check"></i>
                            </span>
                            <span class="text">Completar Salida</span>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Tabla de Vista Detallada -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Vista Detallada de Registros Pendientes</h6>
        </div>
        <div class="table-responsive p-3">
            <table class="table align-items-center table-flush table-responsive-text" id="dataTablePeligrosos" width="100%">
                <thead class="thead-light">
                    <tr>
                        <th><input type="checkbox" id="selectAll-peligrosos"></th>
                        <th>ID</th>
                        <th>Trabajador</th>
                        <th>Residuo</th>
                        <th>Cantidad</th>
                        <th>Unidad</th>
                        <th>CRP</th>
                        <th>Área Generación</th>
                        <th>Ingreso</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th></th>
                        <th>ID</th>
                        <th>Trabajador</th>
                        <th>Residuo</th>
                        <th>Cantidad</th>
                        <th>Unidad</th>
                        <th>CRP</th>
                        <th>Área Generación</th>
                        <th>Ingreso</th>
                    </tr>
                </tfoot>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    var base_url = "<?= base_url() ?>";
    var peligrosos_ajax_url = "<?= base_url('residuos_peligrosos/get_peligrosos_data_ajax') ?>";
    var peligrosos_rango_ajax_url = "<?= base_url('residuos_peligrosos/get_registros_por_rango_ajax') ?>";
</script>

<!-- Modal selector de residuos por lote (Peligrosos) -->
<div class="modal fade" id="selectorResiduoModalP" tabindex="-1" role="dialog" aria-labelledby="selectorResiduoLabelP" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="selectorResiduoLabelP">Selecciona los residuos de este lote</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="lista-residuos-lote-p" class="list-group">
          <!-- items dinámicos -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="btn-agregar-al-buffer-p">Agregar al lote personalizado</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para completar el lote -->
<div class="modal fade" id="loteModal" tabindex="-1" role="dialog" aria-labelledby="loteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="loteModalLabel">Completar Lote de Residuos Peligrosos</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <!-- Vista 1: Formulario -->
      <div id="view-form">
          <form id="loteForm" action="<?= base_url('residuos_peligrosos/procesar_lote'); ?>" method="POST">
            <div class="modal-body">
              <input type="hidden" id="fecha_ingreso" name="fecha_ingreso">
              <p>Vas a completar todos los registros con fecha de ingreso: <b id="fecha_lote_display"></b></p>
              
              <button type="button" class="btn btn-info btn-block mb-3" id="btn-edit-records">
                  <i class="fas fa-edit"></i> Revisar y Editar Registros del Lote
              </button>
              <div class="form-group">
                  <label for="salida">Fecha de Salida</label>
                  <input type="date" class="form-control" id="salida" name="salida" required>
              </div>
              <div class="form-group">
                  <label for="fase_siguiente">Fase de Manejo Siguiente</label>
                  <input type="text" class="form-control" id="fase_siguiente" name="fase_siguiente" required>
              </div>
              <div class="form-group">
                  <label for="destino_razon_social">Nombre o Razón Social (Destino)</label>
                  <input type="text" class="form-control" id="destino_razon_social" name="destino_razon_social" required>
              </div>
              <div class="form-group">
                  <label for="manifiesto">No. de Manifiesto</label>
                  <input type="text" class="form-control" id="manifiesto" name="manifiesto" required>
              </div>
              <div id="edicion-mensaje" class="text-primary mb-3 text-right" style="display: none;">Hay cambios en las cantidades</div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              <button type="submit" class="btn btn-primary">Guardar y Finalizar Lote</button>
            </div>
          </form>
      </div>

      <!-- Vista 2: Tabla Editable (oculta por defecto) -->
      <div id="view-table" style="display: none;">
          <div class="modal-body">
              <h5>Editando registros del <span id="editing-date"></span></h5>
              <div class="table-responsive">
                <table class="table table-sm" id="editableTablePeligrosos">
                    <thead>
                        <tr>
                            <th class="d-none">ID</th>
                            <th>No Retirar</th>
                            <th>Residuo</th>
                            <th>Cantidad Actual</th>
                            <th>Cantidad a Retirar</th>
                            <th>Restante</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Las filas se cargarán aquí con JS -->
                    </tbody>
                </table>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="btn-back-to-form">Volver al Formulario</button>
          </div>
      </div>
    </div>
  </div>
</div> 