$(document).ready(function() {
    
    var dataTableInstance;

    function initializeDataTable(filters = {}) {
        if (dataTableInstance) {
            dataTableInstance.destroy();
        }

        dataTableInstance = $('#dataTable').DataTable({
            "processing": true,
            "serverSide": false, 
            "ajax": {
                "url": baseUrl + "rme/get_rme_terminados_ajax",
                "type": "POST",
                "data": filters
            },
            "columns": [
                { 
                    "data": null,
                    "orderable": false,
                    "searchable": false,
                    "className": "text-center align-middle",
                    "render": function (data, type, row) {
                        return '<input type="checkbox" class="row-select" data-id="' + row.id + '">';
                    }
                },
                { "data": "id" },
                {
                    "data": null,
                    "orderable": false,
                    "searchable": false,
                    "className": "text-center align-middle",
                    "render": function (data, type, row) {
                        var id = row.id;
                        return (
                            '<div class="dropdown">' +
                                '<button class="btn btn-link p-0 text-muted" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Acciones" style="line-height:1;">' +
                                    '<span aria-hidden="true">⋮</span>' +
                                '</button>' +
                                '<div class="dropdown-menu dropdown-menu-right">' +
                                    '<a class="dropdown-item action-delete" href="#" data-id="' + id + '">Eliminar</a>' +
                                '</div>' +
                            '</div>'
                        );
                    }
                },
                { "data": "trabajador" },
                { "data": "residuo" },
                { "data": "clave" },
                { "data": "cantidad" },
                { "data": "unidad" },
                { "data": "almacen" },
                { "data": "area_generacion" },
                { "data": "ingreso" },
                { "data": "salida" },
                { "data": "fase_siguiente" },
                { "data": "destino_razon_social" },
                { "data": "manifiesto" }
            ],
            "columnDefs": [
                { "targets": [1], "visible": false }, // Ocultar columna de ID
                { "targets": [2], "width": 32 } // Ancho para la columna de acciones
            ],
            "responsive": true,
            "pageLength": 25,
            "lengthMenu": [[25, 50, 75, 100, -1], [25, 50, 75, 100, "Todo"]],
            "language": {
                "processing": "Procesando...",
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "emptyTable": "Ningún dato disponible en esta tabla",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "search": "Buscar:",
                "paginate": { "first": "Primero", "last": "Último", "next": "Siguiente", "previous": "Anterior" }
            },
            "dom": 'Blfrtip',
            "buttons": [{
                "text": 'Excel',
                "className": 'btn btn-primary btn-sm mr-2',
                "action": function ( e, dt, node, config ) {
                    var selectedIds = obtenerIDsSeleccionados('dataTable');
                    var url = baseUrl + 'rme/exportar_excel';
                    
                    if (selectedIds.length > 0) {
                        // Si hay seleccionados, enviar los IDs
                        url += '?ids=' + encodeURIComponent(JSON.stringify(selectedIds));
                    } else {
                        // Si no hay seleccionados, usar los filtros actuales
                        var currentFilters = getFilters();
                        
                        // Obtener la información de paginación del DataTables
                        var pageInfo = dt.page.info();
                        currentFilters.start = pageInfo.start;
                        currentFilters.length = pageInfo.length;

                        var queryString = $.param(currentFilters);
                        url += '?' + queryString;
                    }
                    
                    window.location.href = url;
                }
            }]
        });
    }

    function getFilters() {
        var residuos = $('#filtro_residuo').val();
        var fecha_inicio = $('#fecha_inicio').val();
        var fecha_fin = $('#fecha_fin').val();
        
        return {
            residuos: residuos,
            fecha_inicio: fecha_inicio,
            fecha_fin: fecha_fin
        };
    }

    // Cargar la tabla al inicio
    initializeDataTable();

    // Evento para el botón de filtrar
    $('#btn_filter').on('click', function() {
        initializeDataTable(getFilters());
    });

    // Evento para limpiar el filtro
    $('#btn_clear_filter').on('click', function() {
        $('#filter-form')[0].reset();
        $('.selectpicker').selectpicker('refresh');
        initializeDataTable();
    });

    // Manejar checkbox principal
    $(document).on('change', '#selectAll-terminados', function() {
        var table = $('#dataTable').DataTable();
        var checkboxes = table.$('.row-select', {"page": "all"});
        checkboxes.prop('checked', $(this).prop('checked'));
    });

    // Delegación para eliminar
    $('#dataTable tbody').on('click', 'a.action-delete', function(e) {
        e.preventDefault();
        var id = $(this).data('id');

        function doDelete() {
            $.ajax({
                url: baseUrl + 'rme/eliminar_terminado',
                type: 'POST',
                dataType: 'json',
                data: { id: id },
                success: function(resp) {
                    if (resp && resp.status === 'success') {
                        if (window.Swal) Swal.fire('Eliminado', 'Registro eliminado', 'success');
                        dataTableInstance.ajax.reload(null, false);
                    } else {
                        var msg = (resp && resp.message) ? resp.message : 'Error desconocido';
                        if (window.Swal) Swal.fire('Error', msg, 'error'); else alert('Error: ' + msg);
                    }
                },
                error: function() {
                    if (window.Swal) Swal.fire('Error', 'No se pudo completar la solicitud', 'error'); else alert('No se pudo completar la solicitud');
                }
            });
        }

        if (window.Swal) {
            Swal.fire({
                title: '¿Eliminar registro?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(function(result) {
                if (result.isConfirmed) doDelete();
            });
        } else {
            if (confirm('¿Eliminar este registro?')) doDelete();
        }
    });
});

// Función para obtener IDs seleccionados
function obtenerIDsSeleccionados(tableId) {
    var table = $('#' + tableId).DataTable();
    var selectedIds = [];
    
    table.$('.row-select:checked', {"page": "all"}).each(function() {
        selectedIds.push($(this).data('id'));
    });
    
    return selectedIds;
}