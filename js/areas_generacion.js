var table_areas;
$(document).ready(function () {
    table_areas = $("#table_areas").DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            "url": base_url + "Areas_generacion/list_areas",
            "type": "POST"
        },
        "columnDefs": [
            {
                "targets": [0], // Ocultar columna ID
                "visible": false
            },
            {
                "targets": [-1], // Columna de acciones no ordenable
                "orderable": false
            }
        ]
    });

    // Manejar formulario de áreas
    $('#formArea').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var id = $('#area_id').val();
        var url = id ? base_url + 'Areas_generacion/edit/' + id : base_url + 'Areas_generacion/create';
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#modalArea').modal('hide');
                table_areas.ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: id ? 'Área actualizada correctamente' : 'Área agregada correctamente',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function() {
                Swal.fire('Error', 'No se pudo guardar el área', 'error');
            }
        });
    });
});

// Funciones para áreas
function add_area() {
    $('#modalAreaTitle').text('Agregar Área de Generación');
    $('#formArea')[0].reset();
    $('#area_id').val('');
    $('#estado_group').hide();
    $('#modalArea').modal('show');
}

function edit_area(id) {
    $('#modalAreaTitle').text('Editar Área de Generación');
    
    $.ajax({
        url: base_url + 'Areas_generacion/get_by_id/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#area_id').val(data.id);
            $('#area_nombre').val(data.nombre);
            $('#area_descripcion').val(data.descripcion);
            $('#area_activo').prop('checked', data.activo == 1);
            $('#estado_group').show();
            $('#modalArea').modal('show');
        },
        error: function() {
            Swal.fire('Error', 'No se pudo cargar la información del área', 'error');
        }
    });
}

function delete_area(id) {
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
                url: base_url + 'Areas_generacion/delete/' + id,
                type: 'POST',
                success: function(response) {
                    table_areas.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        text: 'El área ha sido eliminada correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo eliminar el área', 'error');
                }
            });
        }
    });
}