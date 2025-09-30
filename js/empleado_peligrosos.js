$(document).ready(function () {
    const container = $('.container-fluid');
    const successMessage = container.data('success-message');
    const peligrososData = container.data('peligrosos-data');

    // SweetAlert alerta con opción de continuar o salir
    if (successMessage) {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: successMessage,
            showCancelButton: true,
            confirmButtonText: 'Sí, hay más residuos',
            cancelButtonText: 'No, terminar sesión',
            allowOutsideClick: false,
            allowEscapeKey: false,
            timer: 30000, // 30 segundos
            timerProgressBar: true,
            didOpen: () => {
                // Mostrar mensaje con letra más grande
                const content = Swal.getHtmlContainer();
                const timerText = document.createElement('div');
                timerText.style.marginTop = '15px';
                timerText.style.fontSize = '18px';
                timerText.style.color = '#333';
                timerText.style.fontWeight = 'bold';
                timerText.innerHTML = '¿Hay más residuos por registrar?';
                content.appendChild(timerText);
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Usuario quiere continuar - limpiar formulario
                $('form')[0].reset();
                $('.selectpicker').selectpicker('refresh');
                
                // Establecer fecha actual automáticamente
                const today = new Date().toISOString().split('T')[0];
                $('#ingreso').val(today);
                
                $('#residuo').focus();
            } else {
                // Usuario eligió "No" o se agotó el tiempo - cerrar sesión y ir al login
                window.location.href = baseUrl + 'auth/logout';
            }
        });
    }

    const residuoSelect = $('#residuo');
    const unidadInput = $('#unidad');
    const crpInput = $('#crp');

    residuoSelect.on('change', function () {
        const selectedResiduo = $(this).val();
        if (selectedResiduo && peligrososData && peligrososData[selectedResiduo]) {
            const data = peligrososData[selectedResiduo];
            unidadInput.val(data.unidad);
            crpInput.val(data.crp);
        } else {
            unidadInput.val('');
            crpInput.val('');
        }
    });

    // No formatear nada - mantener exactamente lo que el usuario escribió
}); 