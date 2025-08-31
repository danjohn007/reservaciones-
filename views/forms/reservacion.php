<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Reservación - Sistema de Reservaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-calendar-check"></i> Nueva Reservación</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?php echo getUrl('index.php?page=dashboard&type=<?php echo $_SESSION['user_type']; ?>'); ?>">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
                <a class="nav-link" href="<?php echo getUrl('index.php?page=logout'); ?>">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center bg-primary text-white">
                        <h4><i class="fas fa-calendar-plus"></i> Nueva Reservación</h4>
                        <p class="mb-0">Complete el formulario para hacer su reservación</p>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fecha_reservacion" class="form-label">Fecha de Reservación *</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                            <input type="date" class="form-control" id="fecha_reservacion" name="fecha_reservacion" 
                                                   min="<?php echo date('Y-m-d'); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="hora_reservacion" class="form-label">Hora de Reservación *</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                            <input type="time" class="form-control" id="hora_reservacion" name="hora_reservacion" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="numero_personas" class="form-label">Número de Personas *</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-users"></i></span>
                                            <input type="number" class="form-control" id="numero_personas" name="numero_personas" 
                                                   min="1" max="20" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="amenidad_id" class="form-label">Mesa/Amenidad</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-utensils"></i></span>
                                            <select class="form-control" id="amenidad_id" name="amenidad_id">
                                                <option value="">Seleccione una amenidad</option>
                                                <?php foreach ($amenidades as $amenidad): ?>
                                                    <option value="<?php echo $amenidad['id']; ?>">
                                                        <?php echo $amenidad['nombre']; ?> 
                                                        (Capacidad: <?php echo $amenidad['capacidad_maxima']; ?> personas)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <small class="form-text text-muted">
                                            Opcional: Seleccione una mesa o amenidad específica
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notas" class="form-label">Notas Adicionales</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                    <textarea class="form-control" id="notas" name="notas" rows="3" 
                                              placeholder="Ocasión especial, preferencias, alergias, etc."></textarea>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-body bg-light">
                                    <h6 class="card-title"><i class="fas fa-info-circle"></i> Información Importante</h6>
                                    <ul class="mb-0">
                                        <li>Las reservaciones están sujetas a confirmación del establecimiento</li>
                                        <li>Recibirá una notificación del estado de su reservación</li>
                                        <li>El costo será determinado por el establecimiento según sus servicios</li>
                                        <li>Puede subir comprobante de pago una vez confirmada la reservación</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-calendar-plus"></i> Crear Reservación
                                </button>
                                <a href="<?php echo getUrl('index.php?page=dashboard&type=<?php echo $_SESSION['user_type']; ?>'); ?>" 
                                   class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validation for number of people vs amenity capacity
        document.getElementById('amenidad_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const numeroPersonas = document.getElementById('numero_personas');
            
            if (selectedOption.value) {
                const capacidad = parseInt(selectedOption.text.match(/Capacidad: (\d+)/)[1]);
                numeroPersonas.max = capacidad;
                
                if (parseInt(numeroPersonas.value) > capacidad) {
                    numeroPersonas.value = capacidad;
                    alert('El número de personas ha sido ajustado a la capacidad máxima de la amenidad seleccionada.');
                }
            } else {
                numeroPersonas.max = 20;
            }
        });
        
        document.getElementById('numero_personas').addEventListener('input', function() {
            const amenidad = document.getElementById('amenidad_id');
            const selectedOption = amenidad.options[amenidad.selectedIndex];
            
            if (selectedOption.value) {
                const capacidad = parseInt(selectedOption.text.match(/Capacidad: (\d+)/)[1]);
                if (parseInt(this.value) > capacidad) {
                    this.value = capacidad;
                    alert('El número de personas no puede exceder la capacidad de la amenidad seleccionada.');
                }
            }
        });
    </script>
</body>
</html>