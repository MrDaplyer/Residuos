$(document).ready(function() {

    var registrosDelLote = []; // Variable para mantener los datos del lote actual
    // Buffer y estado de modo personalizado para Peligrosos
    var bufferSeleccionadosP = {}; // id -> registro
    var currentLoteRegistrosP = []; // registros del lote clickeado
    var modoPersonalizadoP = false; // OFF por defecto

    function actualizarUIBufferP() {
        var total = Object.keys(bufferSeleccionadosP).length;
        $('#badge-seleccionados-p').text(total);
        if (total > 0) {
            $('#btn-finalizar-lote-personalizado-p').prop('disabled', false);
            $('#btn-limpiar-seleccion-p').show();
        } else {
            $('#btn-finalizar-lote-personalizado-p').prop('disabled', true);
            $('#btn-limpiar-seleccion-p').hide();
        }
    }

    function actualizarModoUIP() {
        if (modoPersonalizadoP) {
            $('#btn-lote-personalizado-peligrosos').removeClass('btn-danger').addClass('btn-success');
            $('#btn-lote-personalizado-peligrosos .text').text('Lote Personalizado (activo)');
            $('.lote-card').addClass('border-warning');
            $('body').addClass('personalizado-on');
        } else {
            $('#btn-lote-personalizado-peligrosos').removeClass('btn-success').addClass('btn-danger');
            $('#btn-lote-personalizado-peligrosos .text').text('Lote Personalizado');
            $('.lote-card').removeClass('border-warning');
            $('body').removeClass('personalizado-on');
        }
    }

    $('#loteForm').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var formData = new FormData(this);

        // Obtener los retiros de nuestra variable (la fuente de la verdad)
        var retiros = registrosDelLote.filter(r => r.cantidad_retirada > 0)
                                      .map(r => ({ id: r.id, cantidad_retirada: r.cantidad_retirada }));

        // Función para validar campos requeridos
        function validateForm() {
            var isValid = true;
            var requiredFields = ['salida', 'fase_siguiente', 'destino_razon_social', 'manifiesto'];
            requiredFields.forEach(function(field) {
                var value = $('#' + field).val();
                if (!value || value.trim() === '') {
                    isValid = false;
                    $('#' + field).addClass('is-invalid');
                } else {
                    $('#' + field).removeClass('is-invalid');
                }
            });
            return isValid;
        }

        if (!validateForm()) {
            Swal.fire('Error', 'Por favor, completa todos los campos requeridos.', 'error');
            return;
        }

        var hasChanges = registrosDelLote.length > 0 && registrosDelLote.some(r => parseFloat(r.cantidad_retirada) !== parseFloat(r.cantidad));

        if (hasChanges) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Se modificaron las cantidades del lote. ¿Deseas continuar?',
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'No',
                reverseButtons: true
            }).then((confirmResult) => {
                if (confirmResult.isConfirmed) {
                    checkForZeroRetiros();
                }
            });
        } else {
            checkForZeroRetiros();
        }

        function checkForZeroRetiros() {
            // Caso 1: el usuario NO entró a editar -> auto-cargar y retirar todo
            if (registrosDelLote.length === 0) {
                var fecha = $('#fecha_ingreso').val();
                $.ajax({
                    url: base_url + 'residuos_peligrosos/get_registros_por_fecha_ajax',
                    type: 'POST',
                    data: { fecha: fecha },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            var retiros = response.data.map(function(r) {
                                return { id: r.id, cantidad_retirada: r.cantidad };
                            });
                            proceedToSubmit(formData, retiros);
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo cargar los registros del lote.', 'error');
                    }
                });
                return;
            }

            // Caso 2: el usuario SÍ editó -> validar que exista al menos un retiro > 0
            var retiros = registrosDelLote.filter(r => r.cantidad_retirada > 0)
                                          .map(r => ({ id: r.id, cantidad_retirada: r.cantidad_retirada }));

            if (retiros.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'No se puede finalizar el lote',
                    text: 'Aún hay residuos sin retirar. Ajusta al menos un retiro o desmarca "No Retirar".',
                });
                return; // bloquear envío
            } else {
                proceedToSubmit(formData, retiros);
            }
        }

        // Nota: ya no se permite continuar cuando todos los retiros son 0

        function proceedToSubmit(formData, retiros) {
            retiros.forEach((retiro, index) => {
                formData.append(`retiros[${index}][id]`, retiro.id);
                formData.append(`retiros[${index}][cantidad_retirada]`, retiro.cantidad_retirada);
            });

            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    $('#loteModal').modal('hide');
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Exito!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        }).then(function () {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    $('#loteModal').modal('hide');
                    Swal.fire('Error', 'No se pudo comunicar con el servidor.', 'error');
                }
            });
        }
    });

    // --- Lógica para intercambiar vistas en el modal ---

    function popularTablaEditable() {
        var tbody = $('#editableTablePeligrosos tbody');
        tbody.empty();
        registrosDelLote.forEach(function(registro, index) {
            // valores por defecto
            if (typeof registro.cantidad_retirada === 'undefined') {
                registro.cantidad_retirada = registro.cantidad; // Por defecto retirar todo
            }
            if (typeof registro.no_retirar === 'undefined') {
                registro.no_retirar = false;
            }
            var row = `<tr data-id="${registro.id}">
                          <td class="d-none">${registro.id}</td>
                          <td class="text-center align-middle">
                              <input type="checkbox" class="no-retirar" data-index="${index}" ${registro.no_retirar ? 'checked' : ''} title="No retirar">
                          </td>
                          <td>${registro.residuo}</td>
                          <td>${registro.cantidad}</td>
                          <td><input type="number" step="0.01" class="form-control form-control-sm cantidad-retirar" data-index="${index}" name="cantidad_retirada" value="${registro.cantidad_retirada}" min="0" max="${registro.cantidad}" ${registro.no_retirar ? 'disabled' : ''}></td>
                          <td class="restante-calc"></td>
                       </tr>`;
            tbody.append(row);
        });

        // Calcular restante inicial para cada fila
        registrosDelLote.forEach(function(registro, index) {
            updateRestante(index, registro.cantidad, registro.cantidad_retirada);
        });
    }

    function updateRestante(index, cantidadActual, cantidadRetirar) {
        var restante = parseFloat(cantidadActual) - parseFloat(cantidadRetirar);
        // Redondear a 1 decimal para evitar problemas de punto flotante
        restante = Math.round(restante * 10) / 10;
        var calcText = (cantidadRetirar > 0) ? `<span class="text-danger">= ${restante}</span>` : '';
        $('#editableTablePeligrosos tbody tr').eq(index).find('.restante-calc').html(calcText);
    }

    function submitForm() {
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: formData,
            processData: false, // Importante para FormData
            contentType: false, // Importante para FormData
            dataType: 'json',
            success: function(response) {
                $('#loteModal').modal('hide');

                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Buen trabajo!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 2000
                    }).then(function () {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                $('#loteModal').modal('hide');
                Swal.fire('Error', 'No se pudo comunicar con el servidor.', 'error');
            }
        });
    }

    // Al hacer clic en "Editar Registros"
    $('#btn-edit-records').on('click', function() {
        $('#loteModal .modal-dialog').addClass('modal-lg');
        $('#view-form').hide();
        $('#view-table').show();

        // Si ya tenemos los datos, solo mostramos la tabla
        if (registrosDelLote.length > 0) {
            popularTablaEditable();
        } else {
            // Si no, los cargamos vía AJAX
            var fecha = $('#fecha_ingreso').val();
            $('#editing-date').text($('#fecha_lote_display').text());
            
            $.ajax({
                url: base_url + 'residuos_peligrosos/get_registros_por_fecha_ajax',
                type: 'POST',
                data: { fecha: fecha },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Inicializar nuestra variable con los datos y la cantidad a retirar
                        registrosDelLote = response.data.map(function(r) {
                            r.cantidad_retirada = r.cantidad; // Por defecto se retira todo
                            return r;
                        });
                        popularTablaEditable();
                    } else {
                        console.error("Error al cargar registros: " + response.message);
                    }
                },
                error: function() {
                    console.error("Error de comunicación al cargar registros.");
                }
            });
        }
    });

    // Evento para actualizar cuando el usuario termina de escribir (permite escribir decimales)
    $('#editableTablePeligrosos').on('blur', '.cantidad-retirar', function() {
        var input = $(this);
        var index = input.data('index');
        var nuevoValor = parseFloat(input.val()) || 0;
        var cantidadActual = parseFloat(registrosDelLote[index].cantidad);
        if (nuevoValor > cantidadActual) {
            nuevoValor = cantidadActual;
            input.val(nuevoValor);
        }
        registrosDelLote[index].cantidad_retirada = nuevoValor;
        updateRestante(index, cantidadActual, nuevoValor);
    });

    // Evento para actualizar el cálculo visual mientras escribe (sin resetear el valor)
    $('#editableTablePeligrosos').on('input', '.cantidad-retirar', function() {
        var input = $(this);
        var index = input.data('index');
        var valorActual = input.val();
        
        // Solo actualizar el cálculo visual si el valor es un número válido
        if (valorActual !== '' && !isNaN(parseFloat(valorActual))) {
            var nuevoValor = parseFloat(valorActual);
            var cantidadActual = parseFloat(registrosDelLote[index].cantidad);
            updateRestante(index, cantidadActual, nuevoValor);
        }
    });

    // Toggle No Retirar
    $('#editableTablePeligrosos').on('change', '.no-retirar', function() {
        var chk = $(this);
        var index = chk.data('index');
        var registro = registrosDelLote[index];
        var input = $('#editableTablePeligrosos tbody tr').eq(index).find('.cantidad-retirar');
        var cantidadActual = parseFloat(registro.cantidad);

        if (chk.is(':checked')) {
            registro.prev_cantidad_retirada = registro.cantidad_retirada;
            registro.no_retirar = true;
            registro.cantidad_retirada = 0;
            input.val(0).prop('disabled', true);
            updateRestante(index, cantidadActual, 0);
        } else {
            registro.no_retirar = false;
            var restore = typeof registro.prev_cantidad_retirada !== 'undefined' ? registro.prev_cantidad_retirada : cantidadActual;
            registro.cantidad_retirada = restore;
            input.prop('disabled', false).val(restore);
            updateRestante(index, cantidadActual, restore);
        }
    });

    // Al hacer clic en "Volver al Formulario"
    $('#btn-back-to-form').on('click', function() {
        $('#view-table').hide();
        $('#view-form').show();
        $('#loteModal .modal-dialog').removeClass('modal-lg');

        var hasChanges = registrosDelLote.some(r => parseFloat(r.cantidad_retirada) !== parseFloat(r.cantidad));
        if (hasChanges) {
            $('#edicion-mensaje').show();
        } else {
            $('#edicion-mensaje').hide();
        }
    });

    // Al cerrar el modal, asegurarse de que la vista y tamaño por defecto estén correctos
    $('#loteModal').on('hidden.bs.modal', function () {
        $('#view-table').hide();
        $('#view-form').show();
        $('#loteModal .modal-dialog').removeClass('modal-lg');
        registrosDelLote = []; // Limpiar la variable al cerrar
    });

    // Listener para los botones de completar lote
    $('.btn-completar-lote').on('click', function() {
        var fechaIngreso = $(this).data('fecha');
        var fechaFormateada = new Date(fechaIngreso + 'T00:00:00').toLocaleDateString('es-ES', {
            day: '2-digit', month: '2-digit', year: 'numeric'
        });

        // --- Lógica de fecha automática ---
        var today = new Date().toISOString().split('T')[0];
        $('#loteModal #salida').val(today);

        $('#fecha_ingreso').val(fechaIngreso);
        $('#fecha_lote_display').text(fechaFormateada);
        $('#loteModal').modal('show');
    });

    // --- Nueva lógica: tarjetas de lote clickeables para seleccionar residuos y acumular en buffer ---
    $('.lote-card').on('click', function() {
        if (!modoPersonalizadoP) return;
        var fecha = $(this).data('fecha');
        $.ajax({
            url: base_url + 'residuos_peligrosos/get_registros_por_fecha_ajax',
            type: 'POST',
            data: { fecha: fecha },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    currentLoteRegistrosP = response.data || [];
                    // Agrupar por residuo
                    var agrupado = {};
                    currentLoteRegistrosP.forEach(function(r){
                        var key = r.residuo;
                        if (!agrupado[key]) agrupado[key] = { count: 0, sum: 0 };
                        agrupado[key].count += 1;
                        agrupado[key].sum += parseFloat(r.cantidad || 0);
                    });
                    var cont = $('#lista-residuos-lote-p');
                    cont.empty();
                    Object.keys(agrupado).sort().forEach(function(res){
                        var info = agrupado[res];
                        var item = `
                            <label class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <input type="checkbox" class="mr-2 residuo-checkbox-p" value="${res}">
                                    <span>${res}</span>
                                </div>
                                <span class="badge badge-danger badge-pill">${info.count} / ${info.sum}</span>
                            </label>`;
                        cont.append(item);
                    });
                    $('#selectorResiduoModalP').modal('show');
                } else {
                    Swal.fire('Error', response.message || 'No se pudieron cargar los registros del lote.', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'No se pudo comunicar con el servidor.', 'error');
            }
        });
    });

    // Agregar seleccionados del selector al buffer global (sin SweetAlert de éxito)
    $('#btn-agregar-al-buffer-p').on('click', function(){
        var seleccionados = [];
        $('#lista-residuos-lote-p .residuo-checkbox-p:checked').each(function(){
            seleccionados.push($(this).val());
        });
        if (seleccionados.length === 0) {
            Swal.fire('Atención', 'Selecciona al menos un residuo.', 'warning');
            return;
        }
        currentLoteRegistrosP.forEach(function(r){
            if (seleccionados.indexOf(r.residuo) !== -1) {
                bufferSeleccionadosP[r.id] = r;
            }
        });
        $('#selectorResiduoModalP').modal('hide');
        actualizarUIBufferP();
    });

    // Limpiar selección global
    $('#btn-limpiar-seleccion-p').on('click', function(){
        bufferSeleccionadosP = {};
        actualizarUIBufferP();
    });

    // Finalizar lote personalizado (usar modal existente con tabla)
    $('#btn-finalizar-lote-personalizado-p').on('click', function(){
        var ids = Object.keys(bufferSeleccionadosP);
        if (ids.length === 0) {
            Swal.fire('Atención', 'No hay elementos seleccionados.', 'warning');
            return;
        }
        registrosDelLote = ids.map(function(id){
            var r = bufferSeleccionadosP[id];
            return {
                id: r.id,
                residuo: r.residuo,
                cantidad: r.cantidad,
                unidad: r.unidad,
                area_generacion: r.area_generacion,
                ingreso: r.ingreso,
                cantidad_retirada: r.cantidad,
                no_retirar: false
            };
        });
        // Abrir modal directo en tabla editable
        $('#loteModal #salida').val(new Date().toISOString().split('T')[0]);
        $('#fecha_ingreso').val('');
        $('#fecha_lote_display').text('Lote personalizado (multi-fecha)');
        $('#loteModal').modal('show');
        $('#loteModal .modal-dialog').addClass('modal-lg');
        $('#view-form').hide();
        $('#view-table').show();
        popularTablaEditable();
    });

    // Toggle de modo personalizado
    $('#btn-lote-personalizado-peligrosos').on('click', function(){
        modoPersonalizadoP = !modoPersonalizadoP;
        actualizarModoUIP();
    });

    // Estado inicial
    actualizarModoUIP();
    actualizarUIBufferP();

    // Manejar checkbox principal
    $(document).on('change', '#selectAll-peligrosos', function() {
        var table = $('#dataTablePeligrosos').DataTable();
        var checkboxes = table.$('.row-select', {"page": "all"});
        checkboxes.prop('checked', $(this).prop('checked'));
    });

    // --- Inicialización de DataTables para la vista detallada ---
    $('#dataTablePeligrosos').DataTable({
        "responsive": true,
        "ajax": {
            "url": peligrosos_ajax_url,
            "type": "POST"
        },
        "columns": [
            { 
                "data": null,
                "orderable": false,
                "render": function (data, type, row) {
                    return '<input type="checkbox" class="row-select" data-id="' + row.id + '">';
                }
            },
            { "data": "id" },
            { "data": "trabajador" },
            { "data": "residuo" },
            { "data": "cantidad" },
            { "data": "unidad" },
            { "data": "crp" },
            { "data": "area_generacion" },
            { "data": "ingreso" }
        ],
        "columnDefs": [
            { "targets": [1], "visible": false } 
        ],
        "dom": 'Blfrtip',
        "buttons": [{
            "text": 'Excel',
            "className": 'btn btn-danger btn-sm mr-2',
            "action": function () {
                exportarExcelPendientesPeligrosos();
            }
        }],
        "lengthMenu": [[25, 50, 75, 100, -1], [25, 50, 75, 100, "Todo"]],
        "language": {
            "processing": "Procesando...",
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "emptyTable": "Ningún dato disponible en esta tabla",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_.",
            "infoEmpty": "Mostrando 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero", "last": "Último", "next": "Siguiente", "previous": "Anterior"
            }
        }
    });
});

// Función para obtener IDs seleccionados en Peligrosos
function obtenerIDsSeleccionadosPeligrosos(tableId) {
    var table = $('#' + tableId).DataTable();
    var selectedIds = [];
    
    table.$('.row-select:checked', {"page": "all"}).each(function() {
        selectedIds.push($(this).data('id'));
    });
    
    return selectedIds;
}

// Función para exportar Excel de registros pendientes de Residuos Peligrosos
function exportarExcelPendientesPeligrosos() {
    var selectedIds = obtenerIDsSeleccionadosPeligrosos('dataTablePeligrosos');
    var url = base_url + 'residuos_peligrosos/exportar_excel_pendientes';
    
    if (selectedIds.length > 0) {
        // Si hay seleccionados, enviar los IDs
        url += '?ids=' + encodeURIComponent(JSON.stringify(selectedIds));
    }
    
    window.location.href = url;
} 