</div>
<!-- Footer -->
<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>Copyright &copy; <?= date("Y"); ?> - <?= SITE_NAME; ?>
            </span>
        </div>
    </div>
</footer>
<!-- Footer -->
</div>
</div>

<!-- Scroll to top -->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Scripts bootstrap y AdminLTE -->
<script src="<?= base_url('assets/'); ?>vendor/jquery/jquery.min.js"></script>
<script src="<?= base_url('assets/'); ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/'); ?>vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="<?= base_url('assets/js/erich-jaeger.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= base_url('js/form-helpers.js'); ?>"></script>
<!-- Scripts para usar selects con livesearch -->
<script src="<?= base_url('assets/'); ?>vendor/addins/bootstrap-select.min.js"></script>
<!-- Scripts para usar Datatables -->
<link href="<?= base_url('assets/'); ?>vendor/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
<script src="<?= base_url('assets/'); ?>vendor/datatables/datatables.min.js"></script>
<!-- Scripts para usar datetimepickers -->
<link href="<?= base_url('assets/'); ?>vendor/jquery/jquery.datetimepicker.css" rel="stylesheet" type="text/css" />
<script src="<?= base_url('assets/'); ?>vendor/jquery/jquery.datetimepicker.min.js"></script>
<script src="<?= base_url('assets/'); ?>vendor/addins/moment.min.js"></script>
<!-- Scripts para usar alertas tipo Toast -->
<script src="<?= base_url('assets/'); ?>vendor/jquery/sweetalert2.all.min.js"></script>

<!-- Definir baseUrl para ser usada en archivos JS externos -->
<script>var baseUrl = '<?= base_url(); ?>';</script>

<!-- Cargar script específico de la página si está definido -->
<?php if (isset($page_js)): ?>
    <script src="<?= base_url('js/' . $page_js); ?>?v=<?= time(); ?>"></script>
<?php endif; ?>

<!-- CSS personalizado para mejorar el estilo de Bootstrap Select -->
<style>
/* Mejorar el estilo de Bootstrap Select para que se vea como form-control */
.bootstrap-select .dropdown-toggle {
    background-color: #fff !important;
    border: 1px solid #ced4da !important;
    border-radius: 0.25rem !important;
    color: #495057 !important;
    font-size: 1rem !important;
    font-weight: 400 !important;
    line-height: 1.5 !important;
    padding: 0.375rem 0.75rem !important;
    text-align: left !important;
    box-shadow: none !important;
    min-height: calc(1.5em + 0.75rem + 2px) !important;
}

/* Estilo cuando está enfocado */
.bootstrap-select .dropdown-toggle:focus,
.bootstrap-select.show .dropdown-toggle {
    border-color: #80bdff !important;
    outline: 0 !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
}

/* Estilo del texto placeholder */
.bootstrap-select .dropdown-toggle .filter-option-inner-inner {
    color: #6c757d !important;
}

/* Cuando hay una opción seleccionada */
.bootstrap-select .dropdown-toggle.bs-placeholder .filter-option-inner-inner {
    color: #6c757d !important;
}

.bootstrap-select .dropdown-toggle:not(.bs-placeholder) .filter-option-inner-inner {
    color: #495057 !important;
}

/* Estilo de la flecha dropdown */
.bootstrap-select .dropdown-toggle::after {
    border-top: 0.3em solid #495057 !important;
    border-right: 0.3em solid transparent !important;
    border-bottom: 0 !important;
    border-left: 0.3em solid transparent !important;
}

/* Estilo del dropdown menu */
.bootstrap-select .dropdown-menu {
    border: 1px solid #ced4da !important;
    border-radius: 0.25rem !important;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

/* Estilo de las opciones */
.bootstrap-select .dropdown-menu .dropdown-item {
    padding: 0.5rem 1rem !important;
    color: #495057 !important;
}

.bootstrap-select .dropdown-menu .dropdown-item:hover,
.bootstrap-select .dropdown-menu .dropdown-item:focus {
    background-color: #f8f9fa !important;
    color: #495057 !important;
}

.bootstrap-select .dropdown-menu .dropdown-item.active {
    background-color: #007bff !important;
    color: #fff !important;
}

/* Estilo del campo de búsqueda */
.bootstrap-select .bs-searchbox .form-control {
    border: 1px solid #ced4da !important;
    border-radius: 0.25rem !important;
    margin: 0.5rem !important;
    width: calc(100% - 1rem) !important;
}

/* Remover estilos de botón que no queremos */
.bootstrap-select .dropdown-toggle:not(:disabled):not(.disabled):active,
.bootstrap-select .dropdown-toggle:not(:disabled):not(.disabled).active {
    background-color: #fff !important;
    border-color: #80bdff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
}

/* Asegurar que el ancho sea completo */
.bootstrap-select {
    width: 100% !important;
}

.bootstrap-select .dropdown-toggle {
    width: 100% !important;
}
</style>

<!-- Inicializar Bootstrap Select para selects con muchas opciones -->
<script>
$(document).ready(function() {
    // Inicializar todos los selectpicker
    $('.selectpicker').selectpicker({
        style: '', // Remover estilo por defecto para usar nuestro CSS personalizado
        size: 8,
        liveSearch: true,
        liveSearchPlaceholder: 'Buscar...',
        noneSelectedText: 'Elegir...',
        noneResultsText: 'No se encontraron resultados para {0}',
        countSelectedText: function (numSelected, numTotal) {
            return (numSelected == 1) ? "{0} elemento seleccionado" : "{0} elementos seleccionados";
        }
    });
    
    // Refrescar selectpicker después de cambios dinámicos
    $('.selectpicker').selectpicker('refresh');
});
</script>

</body>

</html>