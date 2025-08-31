<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistema de Reservaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-cog"></i> Admin Panel</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="fas fa-user-shield"></i> <?php echo $_SESSION['user_name']; ?> (Admin)
                </span>
                <a class="nav-link" href="<?php echo getUrl('index.php?page=logout'); ?>">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
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

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?php echo $stats['total_usuarios']; ?></h4>
                                <p class="mb-0">Total Usuarios</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?php echo $stats['usuarios_comercio']; ?></h4>
                                <p class="mb-0">Comercios</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-store fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?php echo $stats['usuarios_cliente']; ?></h4>
                                <p class="mb-0">Clientes</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-user fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4><?php echo count($reservaciones_recientes) + count($compras_recientes) + count($servicios_recientes); ?></h4>
                                <p class="mb-0">Total Solicitudes</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-clipboard-list fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Estadísticas de Reservaciones</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="reservacionesChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Estadísticas de Servicios</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="serviciosChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs for different sections -->
        <ul class="nav nav-tabs" id="adminTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="reservaciones-tab" data-bs-toggle="tab" data-bs-target="#reservaciones" type="button">
                    <i class="fas fa-calendar-check"></i> Reservaciones (<?php echo count($reservaciones_recientes); ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="compras-tab" data-bs-toggle="tab" data-bs-target="#compras" type="button">
                    <i class="fas fa-shopping-cart"></i> Compras (<?php echo count($compras_recientes); ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="servicios-tab" data-bs-toggle="tab" data-bs-target="#servicios" type="button">
                    <i class="fas fa-concierge-bell"></i> Servicios (<?php echo count($servicios_recientes); ?>)
                </button>
            </li>
        </ul>

        <div class="tab-content" id="adminTabContent">
            <!-- Reservaciones Tab -->
            <div class="tab-pane fade show active" id="reservaciones">
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($reservaciones_recientes)): ?>
                            <p class="text-muted text-center">No hay reservaciones aún.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Fecha</th>
                                            <th>Hora</th>
                                            <th>Personas</th>
                                            <th>Amenidad</th>
                                            <th>Estado</th>
                                            <th>Costo</th>
                                            <th>Personal</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($reservaciones_recientes as $reservacion): ?>
                                            <tr>
                                                <td>#<?php echo $reservacion['id']; ?></td>
                                                <td><?php echo $reservacion['cliente_nombre']; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($reservacion['fecha_reservacion'])); ?></td>
                                                <td><?php echo date('H:i', strtotime($reservacion['hora_reservacion'])); ?></td>
                                                <td><?php echo $reservacion['numero_personas']; ?></td>
                                                <td><?php echo $reservacion['amenidad_nombre'] ?? 'N/A'; ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        switch($reservacion['estado']) {
                                                            case 'pendiente': echo 'warning'; break;
                                                            case 'confirmado': echo 'success'; break;
                                                            case 'rechazado': echo 'danger'; break;
                                                            case 'completado': echo 'primary'; break;
                                                            default: echo 'secondary';
                                                        }
                                                    ?>">
                                                        <?php echo ucfirst($reservacion['estado']); ?>
                                                    </span>
                                                </td>
                                                <td>$<?php echo number_format($reservacion['costo_total'], 2); ?></td>
                                                <td><?php echo $reservacion['personal_nombre'] ?? 'Sin asignar'; ?></td>
                                                <td>
                                                    <a href="<?php echo getUrl('index.php?page=reservacion&action=view&id=<?php echo $reservacion['id']; ?>'); ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-secondary" 
                                                            onclick="openUpdateModal('reservacion', <?php echo htmlspecialchars(json_encode($reservacion)); ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Compras Tab -->
            <div class="tab-pane fade" id="compras">
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($compras_recientes)): ?>
                            <p class="text-muted text-center">No hay compras aún.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Tipo Entrega</th>
                                            <th>Fecha Entrega</th>
                                            <th>Estado</th>
                                            <th>Total</th>
                                            <th>Personal</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($compras_recientes as $compra): ?>
                                            <tr>
                                                <td>#<?php echo $compra['id']; ?></td>
                                                <td><?php echo $compra['cliente_nombre']; ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $compra['tipo_entrega'] === 'pickup' ? 'info' : 'secondary'; ?>">
                                                        <?php echo ucfirst($compra['tipo_entrega']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($compra['fecha_entrega'] . ' ' . $compra['hora_entrega'])); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        switch($compra['estado']) {
                                                            case 'pendiente': echo 'warning'; break;
                                                            case 'confirmado': echo 'success'; break;
                                                            case 'rechazado': echo 'danger'; break;
                                                            case 'completado': echo 'primary'; break;
                                                            default: echo 'secondary';
                                                        }
                                                    ?>">
                                                        <?php echo ucfirst($compra['estado']); ?>
                                                    </span>
                                                </td>
                                                <td>$<?php echo number_format($compra['costo_total'], 2); ?></td>
                                                <td><?php echo $compra['personal_nombre'] ?? 'Sin asignar'; ?></td>
                                                <td>
                                                    <a href="<?php echo getUrl('index.php?page=compra&action=view&id=<?php echo $compra['id']; ?>'); ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-secondary" 
                                                            onclick="openUpdateModal('compra', <?php echo htmlspecialchars(json_encode($compra)); ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Servicios Tab -->
            <div class="tab-pane fade" id="servicios">
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($servicios_recientes)): ?>
                            <p class="text-muted text-center">No hay servicios aún.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Fecha</th>
                                            <th>Hora</th>
                                            <th>Descripción</th>
                                            <th>Estado</th>
                                            <th>Costo</th>
                                            <th>Personal</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($servicios_recientes as $servicio): ?>
                                            <tr>
                                                <td>#<?php echo $servicio['id']; ?></td>
                                                <td><?php echo $servicio['cliente_nombre']; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($servicio['fecha_servicio'])); ?></td>
                                                <td><?php echo date('H:i', strtotime($servicio['hora_servicio'])); ?></td>
                                                <td><?php echo substr($servicio['descripcion_servicio'], 0, 50) . '...'; ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        switch($servicio['estado']) {
                                                            case 'pendiente': echo 'warning'; break;
                                                            case 'confirmado': echo 'success'; break;
                                                            case 'rechazado': echo 'danger'; break;
                                                            case 'completado': echo 'primary'; break;
                                                            default: echo 'secondary';
                                                        }
                                                    ?>">
                                                        <?php echo ucfirst($servicio['estado']); ?>
                                                    </span>
                                                </td>
                                                <td>$<?php echo number_format($servicio['costo_total'], 2); ?></td>
                                                <td><?php echo $servicio['personal_nombre'] ?? 'Sin asignar'; ?></td>
                                                <td>
                                                    <a href="<?php echo getUrl('index.php?page=servicio&action=view&id=<?php echo $servicio['id']; ?>'); ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-secondary" 
                                                            onclick="openUpdateModal('servicio', <?php echo htmlspecialchars(json_encode($servicio)); ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Actualizar Solicitud</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo getUrl('index.php?page=update'); ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="tipo" id="modal_tipo">
                        <input type="hidden" name="id" id="modal_id">
                        
                        <div class="mb-3">
                            <label for="modal_estado" class="form-label">Estado</label>
                            <select class="form-control" name="estado" id="modal_estado" required>
                                <option value="pendiente">Pendiente</option>
                                <option value="confirmado">Confirmado</option>
                                <option value="rechazado">Rechazado</option>
                                <option value="completado">Completado</option>
                            </select>
                        </div>
                        
                        <div class="mb-3" id="costo_div">
                            <label for="modal_costo" class="form-label">Costo Total</label>
                            <input type="number" class="form-control" name="costo_total" id="modal_costo" step="0.01" min="0">
                        </div>
                        
                        <div class="mb-3">
                            <label for="modal_personal" class="form-label">Personal Asignado</label>
                            <select class="form-control" name="personal_asignado_id" id="modal_personal">
                                <option value="">Sin asignar</option>
                                <?php 
                                $usuario = new Usuario();
                                $personal = $usuario->getAllByType('comercio');
                                foreach ($personal as $p): ?>
                                    <option value="<?php echo $p['id']; ?>"><?php echo $p['nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="modal_notas" class="form-label">Notas</label>
                            <textarea class="form-control" name="notas" id="modal_notas" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Charts
        <?php
        $reservaciones_data = ['pendiente' => 0, 'confirmado' => 0, 'rechazado' => 0, 'completado' => 0];
        foreach ($stats['reservaciones'] as $stat) {
            $reservaciones_data[$stat['estado']] = $stat['total'];
        }
        
        $servicios_data = ['pendiente' => 0, 'confirmado' => 0, 'rechazado' => 0, 'completado' => 0];
        foreach ($stats['servicios'] as $stat) {
            $servicios_data[$stat['estado']] = $stat['total'];
        }
        ?>
        
        // Reservaciones Chart
        const ctxReservaciones = document.getElementById('reservacionesChart').getContext('2d');
        new Chart(ctxReservaciones, {
            type: 'doughnut',
            data: {
                labels: ['Pendiente', 'Confirmado', 'Rechazado', 'Completado'],
                datasets: [{
                    data: [<?php echo implode(',', array_values($reservaciones_data)); ?>],
                    backgroundColor: ['#ffc107', '#28a745', '#dc3545', '#007bff']
                }]
            }
        });
        
        // Servicios Chart
        const ctxServicios = document.getElementById('serviciosChart').getContext('2d');
        new Chart(ctxServicios, {
            type: 'doughnut',
            data: {
                labels: ['Pendiente', 'Confirmado', 'Rechazado', 'Completado'],
                datasets: [{
                    data: [<?php echo implode(',', array_values($servicios_data)); ?>],
                    backgroundColor: ['#ffc107', '#28a745', '#dc3545', '#007bff']
                }]
            }
        });
        
        function openUpdateModal(tipo, data) {
            document.getElementById('modal_tipo').value = tipo;
            document.getElementById('modal_id').value = data.id;
            document.getElementById('modal_estado').value = data.estado;
            document.getElementById('modal_costo').value = data.costo_total;
            document.getElementById('modal_personal').value = data.personal_asignado_id || '';
            document.getElementById('modal_notas').value = data.notas || '';
            
            // Hide cost field for compras
            if (tipo === 'compra') {
                document.getElementById('costo_div').style.display = 'none';
            } else {
                document.getElementById('costo_div').style.display = 'block';
            }
            
            new bootstrap.Modal(document.getElementById('updateModal')).show();
        }
    </script>
</body>
</html>