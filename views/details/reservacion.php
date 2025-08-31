<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Reservación - Sistema de Reservaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-calendar-check"></i> Detalles de Reservación</a>
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
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <!-- Reservation Details -->
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-calendar-check"></i> Reservación #<?php echo $data['id']; ?></h4>
                        <span class="badge bg-<?php 
                            switch($data['estado']) {
                                case 'pendiente': echo 'warning'; break;
                                case 'confirmado': echo 'success'; break;
                                case 'rechazado': echo 'danger'; break;
                                case 'completado': echo 'primary'; break;
                                default: echo 'secondary';
                            }
                        ?> float-end">
                            <?php echo ucfirst($data['estado']); ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-user"></i> Información del Cliente</h6>
                                <p><strong>Nombre:</strong> <?php echo $data['cliente_nombre']; ?></p>
                                <p><strong>Fecha de Solicitud:</strong> <?php echo date('d/m/Y H:i', strtotime($data['fecha_creacion'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-calendar-alt"></i> Detalles de la Reservación</h6>
                                <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($data['fecha_reservacion'])); ?></p>
                                <p><strong>Hora:</strong> <?php echo date('H:i', strtotime($data['hora_reservacion'])); ?></p>
                                <p><strong>Personas:</strong> <?php echo $data['numero_personas']; ?></p>
                                <p><strong>Amenidad:</strong> <?php echo $data['amenidad_nombre'] ?? 'No especificada'; ?></p>
                            </div>
                        </div>
                        
                        <?php if ($data['notas']): ?>
                            <div class="mt-3">
                                <h6><i class="fas fa-comment"></i> Notas del Cliente</h6>
                                <p class="border p-3 bg-light"><?php echo nl2br(htmlspecialchars($data['notas'])); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h6><i class="fas fa-money-bill-wave"></i> Información de Costos</h6>
                                <p><strong>Costo Total:</strong> $<?php echo number_format($data['costo_total'], 2); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-user-tie"></i> Personal Asignado</h6>
                                <p><?php echo $data['personal_nombre'] ?? 'Sin asignar'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activities -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5><i class="fas fa-history"></i> Historial de Actividades</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($actividades)): ?>
                            <p class="text-muted text-center">No hay actividades registradas aún.</p>
                        <?php else: ?>
                            <div class="timeline">
                                <?php foreach ($actividades as $actividad): ?>
                                    <div class="timeline-item mb-3">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <div class="bg-<?php echo $actividad['tipo_usuario'] === 'cliente' ? 'primary' : 'success'; ?> rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-<?php echo $actividad['tipo_usuario'] === 'cliente' ? 'user' : 'user-tie'; ?> text-white"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="card">
                                                    <div class="card-body py-2">
                                                        <div class="d-flex justify-content-between">
                                                            <strong><?php echo $actividad['usuario_nombre']; ?></strong>
                                                            <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($actividad['fecha_actividad'])); ?></small>
                                                        </div>
                                                        <p class="mb-0"><?php echo htmlspecialchars($actividad['actividad']); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Actions Sidebar -->
            <div class="col-md-4">
                <!-- Upload Receipt -->
                <?php if ($_SESSION['user_type'] === 'cliente' && $data['usuario_id'] == $_SESSION['user_id']): ?>
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-upload"></i> Subir Comprobante</h5>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo getUrl('index.php?page=reservacion&action=upload'); ?>" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="reservacion_id" value="<?php echo $data['id']; ?>">
                                <div class="mb-3">
                                    <label for="comprobante" class="form-label">Archivo de Comprobante</label>
                                    <input type="file" class="form-control" name="comprobante" accept=".jpg,.jpeg,.png,.gif,.pdf" required>
                                    <small class="form-text text-muted">
                                        Formatos permitidos: JPG, PNG, GIF, PDF (máx. 5MB)
                                    </small>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-upload"></i> Subir Comprobante
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Management Actions -->
                <?php if ($_SESSION['user_type'] === 'admin' || $_SESSION['user_type'] === 'comercio'): ?>
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-cogs"></i> Gestionar Reservación</h5>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo getUrl('index.php?page=update'); ?>" method="POST">
                                <input type="hidden" name="tipo" value="reservacion">
                                <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
                                
                                <div class="mb-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select class="form-control" name="estado" required>
                                        <option value="pendiente" <?php echo $data['estado'] === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                        <option value="confirmado" <?php echo $data['estado'] === 'confirmado' ? 'selected' : ''; ?>>Confirmado</option>
                                        <option value="rechazado" <?php echo $data['estado'] === 'rechazado' ? 'selected' : ''; ?>>Rechazado</option>
                                        <option value="completado" <?php echo $data['estado'] === 'completado' ? 'selected' : ''; ?>>Completado</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="costo_total" class="form-label">Costo Total</label>
                                    <input type="number" class="form-control" name="costo_total" step="0.01" min="0" 
                                           value="<?php echo $data['costo_total']; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="personal_asignado_id" class="form-label">Personal Asignado</label>
                                    <select class="form-control" name="personal_asignado_id">
                                        <option value="">Sin asignar</option>
                                        <?php foreach ($personal as $p): ?>
                                            <option value="<?php echo $p['id']; ?>" 
                                                    <?php echo $data['personal_asignado_id'] == $p['id'] ? 'selected' : ''; ?>>
                                                <?php echo $p['nombre']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="notas" class="form-label">Notas Internas</label>
                                    <textarea class="form-control" name="notas" rows="3"><?php echo $data['notas']; ?></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-save"></i> Actualizar Reservación
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Summary Card -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle"></i> Resumen</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Estado:</span>
                            <span class="badge bg-<?php 
                                switch($data['estado']) {
                                    case 'pendiente': echo 'warning'; break;
                                    case 'confirmado': echo 'success'; break;
                                    case 'rechazado': echo 'danger'; break;
                                    case 'completado': echo 'primary'; break;
                                    default: echo 'secondary';
                                }
                            ?>">
                                <?php echo ucfirst($data['estado']); ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Fecha:</span>
                            <span><?php echo date('d/m/Y', strtotime($data['fecha_reservacion'])); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Hora:</span>
                            <span><?php echo date('H:i', strtotime($data['hora_reservacion'])); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Personas:</span>
                            <span><?php echo $data['numero_personas']; ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong>$<?php echo number_format($data['costo_total'], 2); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>