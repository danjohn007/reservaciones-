<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar Servicio - Sistema de Reservaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-warning">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-concierge-bell"></i> Solicitar Servicio</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/public/index.php?page=dashboard&type=<?php echo $_SESSION['user_type']; ?>">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </a>
                <a class="nav-link" href="/public/index.php?page=logout">
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
                    <div class="card-header text-center bg-warning text-dark">
                        <h4><i class="fas fa-bell"></i> Solicitar Servicio Personalizado</h4>
                        <p class="mb-0">Describa el servicio que necesita y le proporcionaremos una cotización</p>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <!-- Service Date and Time -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5><i class="fas fa-calendar-alt"></i> Fecha y Hora del Servicio</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="fecha_servicio" class="form-label">Fecha del Servicio *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                    <input type="date" class="form-control" id="fecha_servicio" name="fecha_servicio" 
                                                           min="<?php echo date('Y-m-d'); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="hora_servicio" class="form-label">Hora del Servicio *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                                    <input type="time" class="form-control" id="hora_servicio" name="hora_servicio" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="numero_personas" class="form-label">Número de Personas</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-users"></i></span>
                                                    <input type="number" class="form-control" id="numero_personas" name="numero_personas" 
                                                           min="1" max="50" value="1">
                                                </div>
                                                <small class="form-text text-muted">
                                                    Número aproximado de personas que participarán en el servicio
                                                </small>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="amenidad_id" class="form-label">Espacio/Amenidad Requerida</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                                                    <select class="form-control" id="amenidad_id" name="amenidad_id">
                                                        <option value="">No requiere espacio específico</option>
                                                        <?php foreach ($amenidades as $amenidad): ?>
                                                            <option value="<?php echo $amenidad['id']; ?>">
                                                                <?php echo $amenidad['nombre']; ?> 
                                                                (Capacidad: <?php echo $amenidad['capacidad_maxima']; ?> personas)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <small class="form-text text-muted">
                                                    Opcional: Si requiere un espacio específico para el servicio
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Service Description -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5><i class="fas fa-clipboard-list"></i> Descripción del Servicio</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="descripcion_servicio" class="form-label">Descripción Detallada del Servicio *</label>
                                        <textarea class="form-control" id="descripcion_servicio" name="descripcion_servicio" 
                                                  rows="5" required placeholder="Describa detalladamente el servicio que necesita...">
                                        </textarea>
                                        <small class="form-text text-muted">
                                            Incluya todos los detalles posibles: tipo de evento, servicios específicos, equipo necesario, 
                                            personal requerido, etc.
                                        </small>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h6 class="card-title"><i class="fas fa-lightbulb"></i> Ejemplos de Servicios</h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li>Organización de eventos corporativos</li>
                                                                <li>Catering para celebraciones</li>
                                                                <li>Servicios de fotografía</li>
                                                                <li>Decoración de eventos</li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <ul class="mb-0">
                                                                <li>Servicios de entretenimiento</li>
                                                                <li>Alquiler de equipo audiovisual</li>
                                                                <li>Servicios de limpieza especializada</li>
                                                                <li>Consultoría personalizada</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Additional Information -->
                            <div class="mb-3">
                                <label for="notas" class="form-label">Información Adicional</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                    <textarea class="form-control" id="notas" name="notas" rows="3" 
                                              placeholder="Presupuesto aproximado, preferencias específicas, contacto adicional, etc."></textarea>
                                </div>
                                <small class="form-text text-muted">
                                    Incluya cualquier información adicional que pueda ser útil para la cotización
                                </small>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-body bg-info text-white">
                                    <h6 class="card-title"><i class="fas fa-info-circle"></i> Proceso de Solicitud</h6>
                                    <ol class="mb-0">
                                        <li>Envíe su solicitud con la descripción detallada del servicio</li>
                                        <li>Nuestro equipo revisará su solicitud y contactará con usted</li>
                                        <li>Recibirá una cotización personalizada con costos y tiempos</li>
                                        <li>Una vez aprobada, se asignará personal especializado</li>
                                        <li>Podrá dar seguimiento al estado de su servicio en tiempo real</li>
                                    </ol>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <i class="fas fa-paper-plane"></i> Enviar Solicitud de Servicio
                                </button>
                                <a href="/public/index.php?page=dashboard&type=<?php echo $_SESSION['user_type']; ?>" 
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
                    alert('El número de personas ha sido ajustado a la capacidad máxima del espacio seleccionado.');
                }
            } else {
                numeroPersonas.max = 50;
            }
        });
        
        document.getElementById('numero_personas').addEventListener('input', function() {
            const amenidad = document.getElementById('amenidad_id');
            const selectedOption = amenidad.options[amenidad.selectedIndex];
            
            if (selectedOption.value) {
                const capacidad = parseInt(selectedOption.text.match(/Capacidad: (\d+)/)[1]);
                if (parseInt(this.value) > capacidad) {
                    this.value = capacidad;
                    alert('El número de personas no puede exceder la capacidad del espacio seleccionado.');
                }
            }
        });
        
        // Character counter for description
        const descripcionTextarea = document.getElementById('descripcion_servicio');
        const charCountDiv = document.createElement('div');
        charCountDiv.className = 'text-muted small text-end mt-1';
        descripcionTextarea.parentNode.appendChild(charCountDiv);
        
        function updateCharCount() {
            const length = descripcionTextarea.value.length;
            charCountDiv.textContent = `${length} caracteres`;
            
            if (length < 50) {
                charCountDiv.className = 'text-danger small text-end mt-1';
                charCountDiv.textContent += ' (mínimo 50 caracteres recomendado)';
            } else {
                charCountDiv.className = 'text-muted small text-end mt-1';
            }
        }
        
        descripcionTextarea.addEventListener('input', updateCharCount);
        updateCharCount();
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const descripcion = descripcionTextarea.value.trim();
            
            if (descripcion.length < 20) {
                e.preventDefault();
                alert('Por favor, proporcione una descripción más detallada del servicio (mínimo 20 caracteres).');
                descripcionTextarea.focus();
                return;
            }
        });
    </script>
</body>
</html>