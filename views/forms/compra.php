<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Compra - Sistema de Reservaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-shopping-cart"></i> Nueva Compra</a>
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

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center bg-success text-white">
                        <h4><i class="fas fa-shopping-bag"></i> Nueva Compra</h4>
                        <p class="mb-0">Seleccione productos y configure la entrega</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="compraForm">
                            <!-- Delivery Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5><i class="fas fa-truck"></i> Información de Entrega</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="tipo_entrega" class="form-label">Tipo de Entrega *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-shipping-fast"></i></span>
                                                    <select class="form-control" id="tipo_entrega" name="tipo_entrega" required>
                                                        <option value="">Seleccione tipo de entrega</option>
                                                        <option value="pickup">Recoger en Tienda (Pickup)</option>
                                                        <option value="domicilio">Envío a Domicilio</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="fecha_entrega" class="form-label">Fecha de Entrega *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                                    <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" 
                                                           min="<?php echo date('Y-m-d'); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="hora_entrega" class="form-label">Hora de Entrega *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                                    <input type="time" class="form-control" id="hora_entrega" name="hora_entrega" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3" id="direccion_div" style="display: none;">
                                                <label for="direccion_entrega" class="form-label">Dirección de Entrega *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                                    <textarea class="form-control" id="direccion_entrega" name="direccion_entrega" 
                                                              rows="2" placeholder="Dirección completa para envío"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Product Selection -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5><i class="fas fa-list"></i> Selección de Productos</h5>
                                </div>
                                <div class="card-body">
                                    <div id="productos-container">
                                        <?php foreach ($productos as $index => $producto): ?>
                                            <div class="row align-items-center mb-3 producto-row">
                                                <div class="col-md-1">
                                                    <div class="form-check">
                                                        <input class="form-check-input producto-check" type="checkbox" 
                                                               name="productos[]" value="<?php echo $producto['id']; ?>" 
                                                               id="producto_<?php echo $producto['id']; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <label for="producto_<?php echo $producto['id']; ?>" class="form-label">
                                                        <strong><?php echo $producto['nombre']; ?></strong><br>
                                                        <small class="text-muted"><?php echo $producto['descripcion']; ?></small><br>
                                                        <span class="badge bg-info">Stock: <?php echo $producto['stock']; ?></span>
                                                    </label>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="input-group">
                                                        <span class="input-group-text">Cant.</span>
                                                        <input type="number" class="form-control cantidad-input" 
                                                               name="cantidades[]" min="1" max="<?php echo $producto['stock']; ?>" 
                                                               value="1" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="text-end">
                                                        <strong>$<?php echo number_format($producto['precio'], 2); ?></strong><br>
                                                        <small class="text-muted">c/u</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Nota:</strong> Seleccione los productos que desea comprar marcando la casilla y especificando la cantidad.
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Additional Notes -->
                            <div class="mb-3">
                                <label for="notas" class="form-label">Notas Adicionales</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-comment"></i></span>
                                    <textarea class="form-control" id="notas" name="notas" rows="3" 
                                              placeholder="Instrucciones especiales, preferencias, etc."></textarea>
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-body bg-light">
                                    <h6 class="card-title"><i class="fas fa-info-circle"></i> Información Importante</h6>
                                    <ul class="mb-0">
                                        <li>Las compras están sujetas a confirmación y disponibilidad de stock</li>
                                        <li>El total final incluirá impuestos y costos de envío (si aplica)</li>
                                        <li>Puede subir comprobante de pago una vez confirmada la compra</li>
                                        <li>Los tiempos de entrega pueden variar según disponibilidad</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-shopping-cart"></i> Realizar Compra
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
            
            <!-- Order Summary -->
            <div class="col-md-4">
                <div class="card sticky-top">
                    <div class="card-header">
                        <h5><i class="fas fa-receipt"></i> Resumen de Compra</h5>
                    </div>
                    <div class="card-body">
                        <div id="resumen-productos">
                            <p class="text-muted text-center">No hay productos seleccionados</p>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total Estimado:</strong>
                            <strong id="total-estimado">$0.00</strong>
                        </div>
                        <small class="text-muted">*El total final puede variar según impuestos y envío</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show/hide address field based on delivery type
        document.getElementById('tipo_entrega').addEventListener('change', function() {
            const direccionDiv = document.getElementById('direccion_div');
            const direccionInput = document.getElementById('direccion_entrega');
            
            if (this.value === 'domicilio') {
                direccionDiv.style.display = 'block';
                direccionInput.required = true;
            } else {
                direccionDiv.style.display = 'none';
                direccionInput.required = false;
                direccionInput.value = '';
            }
        });
        
        // Enable/disable quantity input based on product selection
        document.querySelectorAll('.producto-check').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const row = this.closest('.producto-row');
                const cantidadInput = row.querySelector('.cantidad-input');
                
                if (this.checked) {
                    cantidadInput.disabled = false;
                } else {
                    cantidadInput.disabled = true;
                    cantidadInput.value = 1;
                }
                
                updateResumen();
            });
        });
        
        // Update summary when quantities change
        document.querySelectorAll('.cantidad-input').forEach(function(input) {
            input.addEventListener('input', updateResumen);
        });
        
        function updateResumen() {
            const resumenDiv = document.getElementById('resumen-productos');
            const totalDiv = document.getElementById('total-estimado');
            let html = '';
            let total = 0;
            
            document.querySelectorAll('.producto-check:checked').forEach(function(checkbox) {
                const row = checkbox.closest('.producto-row');
                const nombre = row.querySelector('label strong').textContent;
                const cantidadInput = row.querySelector('.cantidad-input');
                const cantidad = parseInt(cantidadInput.value) || 0;
                const precioText = row.querySelector('.text-end strong').textContent;
                const precio = parseFloat(precioText.replace('$', '').replace(',', ''));
                const subtotal = cantidad * precio;
                
                html += `
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <strong>${nombre}</strong><br>
                            <small>${cantidad} x $${precio.toFixed(2)}</small>
                        </div>
                        <div>$${subtotal.toFixed(2)}</div>
                    </div>
                `;
                
                total += subtotal;
            });
            
            if (html === '') {
                html = '<p class="text-muted text-center">No hay productos seleccionados</p>';
            }
            
            resumenDiv.innerHTML = html;
            totalDiv.textContent = '$' + total.toFixed(2);
        }
        
        // Form validation
        document.getElementById('compraForm').addEventListener('submit', function(e) {
            const productosSeleccionados = document.querySelectorAll('.producto-check:checked').length;
            
            if (productosSeleccionados === 0) {
                e.preventDefault();
                alert('Debe seleccionar al menos un producto para realizar la compra.');
                return;
            }
        });
    </script>
</body>
</html>