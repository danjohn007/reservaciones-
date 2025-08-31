<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Cliente - Sistema de Reservaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-calendar-check"></i> Reservaciones</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="fas fa-user"></i> <?php echo $_SESSION['user_name']; ?> (Cliente)
                </span>
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

        <!-- Welcome Section -->
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Bienvenido, <?php echo $_SESSION['user_name']; ?></h2>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-calendar-plus fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Nueva Reservación</h5>
                        <p class="card-text">Haga una reservación para mesa o amenidad</p>
                        <a href="<?php echo getUrl('index.php?page=form&type=reservacion'); ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Reservación
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-shopping-cart fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Comprar Productos</h5>
                        <p class="card-text">Ordene productos para pickup o domicilio</p>
                        <a href="<?php echo getUrl('index.php?page=form&type=compra'); ?>" class="btn btn-success">
                            <i class="fas fa-shopping-bag"></i> Hacer Compra
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-concierge-bell fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">Solicitar Servicio</h5>
                        <p class="card-text">Solicite servicios personalizados</p>
                        <a href="<?php echo getUrl('index.php?page=form&type=servicio'); ?>" class="btn btn-warning">
                            <i class="fas fa-bell"></i> Solicitar Servicio
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs for different sections -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="reservaciones-tab" data-bs-toggle="tab" data-bs-target="#reservaciones" type="button">
                    <i class="fas fa-calendar-check"></i> Mis Reservaciones (<?php echo count($mis_reservaciones); ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="compras-tab" data-bs-toggle="tab" data-bs-target="#compras" type="button">
                    <i class="fas fa-shopping-cart"></i> Mis Compras (<?php echo count($mis_compras); ?>)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="servicios-tab" data-bs-toggle="tab" data-bs-target="#servicios" type="button">
                    <i class="fas fa-concierge-bell"></i> Mis Servicios (<?php echo count($mis_servicios); ?>)
                </button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <!-- Reservaciones Tab -->
            <div class="tab-pane fade show active" id="reservaciones">
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($mis_reservaciones)): ?>
                            <p class="text-muted text-center">No tiene reservaciones aún.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Hora</th>
                                            <th>Personas</th>
                                            <th>Amenidad</th>
                                            <th>Estado</th>
                                            <th>Costo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($mis_reservaciones as $reservacion): ?>
                                            <tr>
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
                                                <td>
                                                    <a href="<?php echo getUrl('index.php?page=reservacion&action=view&id=<?php echo $reservacion['id']; ?>'); ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
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
                        <?php if (empty($mis_compras)): ?>
                            <p class="text-muted text-center">No tiene compras aún.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Fecha Compra</th>
                                            <th>Tipo Entrega</th>
                                            <th>Fecha Entrega</th>
                                            <th>Estado</th>
                                            <th>Total</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($mis_compras as $compra): ?>
                                            <tr>
                                                <td><?php echo date('d/m/Y H:i', strtotime($compra['fecha_creacion'])); ?></td>
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
                                                <td>
                                                    <a href="<?php echo getUrl('index.php?page=compra&action=view&id=<?php echo $compra['id']; ?>'); ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
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
                        <?php if (empty($mis_servicios)): ?>
                            <p class="text-muted text-center">No tiene servicios solicitados aún.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Fecha Servicio</th>
                                            <th>Hora</th>
                                            <th>Descripción</th>
                                            <th>Estado</th>
                                            <th>Costo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($mis_servicios as $servicio): ?>
                                            <tr>
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
                                                <td>
                                                    <a href="<?php echo getUrl('index.php?page=servicio&action=view&id=<?php echo $servicio['id']; ?>'); ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>