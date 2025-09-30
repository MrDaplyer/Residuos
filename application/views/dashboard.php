<div class="container-fluid">
    <div class="row justify-content-center align-items-center" style="min-height: 60vh;">
        <div class="col-lg-4 col-md-6 col-sm-8">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <h2 class="h3 text-gray-800 mb-4">Seleccione el tipo de residuo</h2>
                    </div>
                    
                    <div class="d-flex flex-column align-items-center">
                        <!-- Botón RME (No Peligrosos) - Arriba -->
                        <a href="<?= base_url('empleado/rme'); ?>" class="btn btn-primary btn-lg mb-3" style="width: 250px; padding: 12px;">
                            <i class="fas fa-leaf mr-2"></i>
                            No Peligrosos (RME)
                        </a>
                        
                        <!-- Botón Peligrosos - Abajo -->
                        <a href="<?= base_url('empleado/peligrosos'); ?>" class="btn btn-danger btn-lg" style="width: 250px; padding: 12px;">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Peligrosos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>