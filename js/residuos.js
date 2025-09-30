var table_rme, table_peligrosos;
$(document).ready(function () {
    table_rme = $("#table_rme").DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            "url": base_url + "Residuos/list_rme",
            "type": "POST"
        },
        "columnDefs": [
            {
                "targets": [0], 
                "visible": false
            },
            {
                "targets": [-1], 
                "orderable": false
            }
        ]
    });

    table_peligrosos = $("#table_peligrosos").DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            "url": base_url + "Residuos/list_peligrosos",
            "type": "POST"
        },
        "columnDefs": [
            {
                "targets": [0], 
                "visible": false
            },
            {
                "targets": [-1],
                "orderable": false
            }
        ]
    });

    // Manejar formulario RME
    $('#formRME').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var id = $('#rme_id').val();
        var url = id ? base_url + 'Residuos/edit/' + id : base_url + 'Residuos/create';
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#modalRME').modal('hide');
                table_rme.ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: id ? 'Residuo actualizado correctamente' : 'Residuo agregado correctamente',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function() {
                Swal.fire('Error', 'No se pudo guardar el residuo', 'error');
            }
        });
    });

    // Manejar formulario Peligrosos
    $('#formPeligroso').on('submit', function(e) {
        e.preventDefault();
        
        updateCRPValue();
        
        var formData = $(this).serialize();
        var id = $('#peligroso_id').val();
        var url = id ? base_url + 'Residuos/edit_peligroso/' + id : base_url + 'Residuos/create_peligroso';
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#modalPeligroso').modal('hide');
                table_peligrosos.ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: id ? 'Residuo actualizado correctamente' : 'Residuo agregado correctamente',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function() {
                Swal.fire('Error', 'No se pudo guardar el residuo', 'error');
            }
        });
    });

    // Manejar cambios en los checkboxes de CRP
    $('#crp_te, #crp_i').on('change', function() {
        updateCRPValue();
    });
});

// Funciones para RME
function add_residuo() {
    $('#modalRMETitle').text('Agregar Residuo RME');
    $('#formRME')[0].reset();
    $('#rme_id').val('');
    $('#modalRME').modal('show');
}

function edit_residuo(id) {
    $('#modalRMETitle').text('Editar Residuo RME');
    
    $.ajax({
        url: base_url + 'Residuos/get_by_id/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#rme_id').val(data.id);
            $('#rme_nombre').val(data.nombre);
            $('#rme_clave').val(data.clave);
            $('#rme_unidad').val(data.unidad);
            $('#rme_almacen').val(data.almacen);
            $('#modalRME').modal('show');
        },
        error: function() {
            Swal.fire('Error', 'No se pudo cargar la información del residuo', 'error');
        }
    });
}

function delete_residuo(id) {
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
                url: base_url + 'Residuos/delete/' + id,
                type: 'POST',
                success: function(response) {
                    table_rme.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        text: 'El residuo ha sido eliminado correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo eliminar el residuo', 'error');
                }
            });
        }
    });
}

// Funciones para Peligrosos
function add_peligroso() {
    $('#modalPeligrosoTitle').text('Agregar Residuo Peligroso');
    $('#formPeligroso')[0].reset();
    $('#peligroso_id').val('');
    $('#modalPeligroso').modal('show');
}

function edit_peligroso(id) {
    $('#modalPeligrosoTitle').text('Editar Residuo Peligroso');
    
    $.ajax({
        url: base_url + 'Residuos/get_peligroso_by_id/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#peligroso_id').val(data.id);
            $('#peligroso_nombre').val(data.nombre);
            $('#peligroso_unidad').val(data.unidad);
            $('#peligroso_crp').val(data.crp);
            
            setCRPCheckboxes(data.crp);
            
            $('#modalPeligroso').modal('show');
        },
        error: function() {
            Swal.fire('Error', 'No se pudo cargar la información del residuo', 'error');
        }
    });
}

function delete_peligroso(id) {
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
                url: base_url + 'Residuos/delete_peligroso/' + id,
                type: 'POST',
                success: function(response) {
                    table_peligrosos.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        text: 'El residuo ha sido eliminado correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function() {
                    Swal.fire('Error', 'No se pudo eliminar el residuo', 'error');
                }
            });
        }
    });
}

function updateCRPValue() {
    var crpValues = [];
    
    if ($('#crp_te').is(':checked')) {
        crpValues.push('Te');
    }
    
    if ($('#crp_i').is(':checked')) {
        crpValues.push('I');
    }
    
    var crpString = crpValues.join(', ');
    $('#peligroso_crp').val(crpString);
}

function setCRPCheckboxes(crpValue) {
    $('#crp_te').prop('checked', false);
    $('#crp_i').prop('checked', false);
    
    if (crpValue) {
        var crpArray = crpValue.split(',').map(function(item) {
            return item.trim();
        });
        
        if (crpArray.includes('Te')) {
            $('#crp_te').prop('checked', true);
        }
        
        if (crpArray.includes('I')) {
            $('#crp_i').prop('checked', true);
        }
    }
}

// Función para limpiar checkboxes al agregar nuevo residuo
function add_peligroso() {
    $('#modalPeligrosoTitle').text('Agregar Residuo Peligroso');
    $('#formPeligroso')[0].reset();
    $('#peligroso_id').val('');
    
    $('#crp_te').prop('checked', false);
    $('#crp_i').prop('checked', false);
    $('#peligroso_crp').val('');
    
    $('#modalPeligroso').modal('show');
}