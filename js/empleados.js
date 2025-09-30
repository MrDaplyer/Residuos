$(document).ready(function() {
    
    // Inicializar DataTable
    $('#empleadosTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": empleadosAjaxUrl,
            "type": "POST"
        },
        "columns": [
            { "data": "id", "visible": false },
            { "data": "NumEmpleado" },
            { "data": "nombre" },
            { "data": "rol" },
            { "data": "acciones", "orderable": false }
        ],
        "language": {
            "processing": "Procesando...",
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron empleados",
            "emptyTable": "No hay empleados registrados",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
            "infoEmpty": "Mostrando 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último", 
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        "pageLength": 25,
        "order": [[1, 'asc']]
    });

    // Manejar checkbox de administrador
    $('#es_admin').change(function() {
        if ($(this).is(':checked')) {
            $('#contrasenaGroup').show();
            $('#contrasena').attr('required', true);
        } else {
            $('#contrasenaGroup').hide();
            $('#contrasena').removeAttr('required').val('');
        }
    });

    // Manejar envío del formulario
    $('#empleadoForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var empleadoId = $('#empleado_id').val();
        var url = empleadoId ? baseUrl + 'empleados/editar/' + empleadoId : baseUrl + 'empleados/crear';
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#empleadoModal').modal('hide');
                    $('#empleadosTable').DataTable().ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo comunicar con el servidor'
                });
            }
        });
    });

    // Limpiar formulario al cerrar modal
    $('#empleadoModal').on('hidden.bs.modal', function() {
        $('#empleadoForm')[0].reset();
        $('#empleado_id').val('');
        $('#contrasenaGroup').hide();
        $('#contrasena').removeAttr('required');
        $('#es_admin').prop('checked', false);
    });
});

// Función para nuevo empleado
function nuevoEmpleado() {
    $('#empleadoModalLabel').html('<i class="fas fa-user-plus mr-2"></i>NUEVO EMPLEADO');
    $('#empleadoModal').modal('show');
}

// Función para editar empleado
function editarEmpleado(id) {
    $('#empleadoModalLabel').html('<i class="fas fa-user-edit mr-2"></i>EDITAR EMPLEADO');
    
    $.ajax({
        url: baseUrl + 'empleados/obtener/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                var empleado = response.data;
                $('#empleado_id').val(empleado.id);
                $('#NumEmpleado').val(empleado.NumEmpleado);
                $('#nombre').val(empleado.nombre);
                
                if (empleado.rol == 1) {
                    $('#es_admin').prop('checked', true);
                    $('#contrasenaGroup').show();
                    $('#contrasena').attr('required', false); // No requerir contraseña en edición
                } else {
                    $('#es_admin').prop('checked', false);
                    $('#contrasenaGroup').hide();
                }
                
                $('#empleadoModal').modal('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cargar la información del empleado'
            });
        }
    });
}

// Función para eliminar empleado
function eliminarEmpleado(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: baseUrl + 'empleados/eliminar/' + id,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#empleadosTable').DataTable().ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo eliminar el empleado'
                    });
                }
            });
        }
    });
}