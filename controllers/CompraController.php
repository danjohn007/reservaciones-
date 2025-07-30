<?php

require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Servicio.php';
require_once __DIR__ . '/AuthController.php';

class CompraController {
    
    public function create() {
        AuthController::requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo_entrega = $_POST['tipo_entrega'];
            $direccion_entrega = $_POST['direccion_entrega'] ?? '';
            $fecha_entrega = $_POST['fecha_entrega'];
            $hora_entrega = $_POST['hora_entrega'];
            $productos = $_POST['productos'] ?? [];
            $cantidades = $_POST['cantidades'] ?? [];
            $notas = $_POST['notas'] ?? '';
            
            // Validaciones
            if (empty($tipo_entrega) || empty($fecha_entrega) || empty($hora_entrega) || empty($productos)) {
                $_SESSION['error'] = 'Por favor complete todos los campos obligatorios y seleccione al menos un producto';
                header('Location: /public/index.php?page=form&type=compra');
                exit;
            }
            
            if ($tipo_entrega === 'domicilio' && empty($direccion_entrega)) {
                $_SESSION['error'] = 'La dirección de entrega es obligatoria para envío a domicilio';
                header('Location: /public/index.php?page=form&type=compra');
                exit;
            }
            
            // Validar que la fecha no sea en el pasado
            if (strtotime($fecha_entrega) < strtotime(date('Y-m-d'))) {
                $_SESSION['error'] = 'No puede programar entregas para fechas pasadas';
                header('Location: /public/index.php?page=form&type=compra');
                exit;
            }
            
            // Crear la compra
            $compra = new Compra();
            $compra->usuario_id = $_SESSION['user_id'];
            $compra->tipo_entrega = $tipo_entrega;
            $compra->direccion_entrega = $direccion_entrega;
            $compra->fecha_entrega = $fecha_entrega;
            $compra->hora_entrega = $hora_entrega;
            $compra->notas = $notas;
            
            if ($compra->create()) {
                // Agregar detalles de productos
                $producto_model = new Producto();
                $total_error = false;
                
                foreach ($productos as $index => $producto_id) {
                    if (empty($cantidades[$index]) || $cantidades[$index] <= 0) {
                        continue;
                    }
                    
                    if ($producto_model->findById($producto_id)) {
                        // Verificar stock disponible
                        if ($producto_model->stock >= $cantidades[$index]) {
                            // Agregar detalle
                            $compra->addDetalle($producto_id, $cantidades[$index], $producto_model->precio);
                            
                            // Actualizar stock
                            $producto_model->updateStock($cantidades[$index]);
                        } else {
                            $total_error = true;
                            $_SESSION['error'] = "Stock insuficiente para el producto: " . $producto_model->nombre;
                            break;
                        }
                    }
                }
                
                if (!$total_error) {
                    // Actualizar total de la compra
                    $compra->updateTotal();
                    
                    // Registrar actividad
                    $actividad = new Actividad();
                    $actividad->tipo_solicitud = 'compra';
                    $actividad->solicitud_id = $compra->id;
                    $actividad->usuario_id = $_SESSION['user_id'];
                    $actividad->actividad = "Compra creada para entrega el $fecha_entrega a las $hora_entrega ($tipo_entrega)";
                    $actividad->create();
                    
                    $_SESSION['success'] = 'Compra creada exitosamente';
                    header('Location: /public/index.php?page=dashboard&type=' . $_SESSION['user_type']);
                    exit;
                }
            }
            
            $_SESSION['error'] = $_SESSION['error'] ?? 'Error al crear la compra';
            header('Location: /public/index.php?page=form&type=compra');
            exit;
        }
        
        // Obtener productos disponibles
        $producto = new Producto();
        $productos = $producto->getAll();
        
        include __DIR__ . '/../views/forms/compra.php';
    }
    
    public function view($id) {
        AuthController::requireAuth();
        
        $compra = new Compra();
        $data = $compra->findById($id);
        
        if (!$data) {
            $_SESSION['error'] = 'Compra no encontrada';
            header('Location: /public/index.php?page=dashboard&type=' . $_SESSION['user_type']);
            exit;
        }
        
        // Verificar permisos
        if ($_SESSION['user_type'] === 'cliente' && $data['usuario_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'No tiene permisos para ver esta compra';
            header('Location: /public/index.php?page=dashboard&type=cliente');
            exit;
        }
        
        // Obtener detalles de la compra
        $detalles = $compra->getDetalles($id);
        
        // Obtener actividades
        $actividad = new Actividad();
        $actividades = $actividad->getBySolicitud('compra', $id);
        
        // Obtener personal disponible para asignación
        $usuario = new Usuario();
        $personal = $usuario->getAllByType('comercio');
        
        include __DIR__ . '/../views/details/compra.php';
    }
    
    public function uploadComprobante() {
        AuthController::requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['comprobante'])) {
            $id = $_POST['compra_id'];
            $file = $_FILES['comprobante'];
            
            // Validar que el usuario tenga acceso a esta compra
            $compra = new Compra();
            $data = $compra->findById($id);
            
            if (!$data || ($_SESSION['user_type'] === 'cliente' && $data['usuario_id'] != $_SESSION['user_id'])) {
                $_SESSION['error'] = 'Compra no encontrada o sin permisos';
                header('Location: /public/index.php?page=dashboard&type=' . $_SESSION['user_type']);
                exit;
            }
            
            // Validar archivo
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
            if (!in_array($file['type'], $allowed_types)) {
                $_SESSION['error'] = 'Tipo de archivo no permitido. Use JPEG, PNG, GIF o PDF';
                header('Location: /public/index.php?page=compra&action=view&id=' . $id);
                exit;
            }
            
            if ($file['size'] > 5 * 1024 * 1024) { // 5MB
                $_SESSION['error'] = 'El archivo es muy grande. Máximo 5MB';
                header('Location: /public/index.php?page=compra&action=view&id=' . $id);
                exit;
            }
            
            // Crear directorio si no existe
            $upload_dir = UPLOAD_PATH . 'comprobantes/compras/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Generar nombre único
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'compra_' . $id . '_' . time() . '.' . $extension;
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Guardar en base de datos
                $conn = getDB();
                $query = "INSERT INTO comprobantes_pago (tipo_solicitud, solicitud_id, nombre_archivo, ruta_archivo) 
                         VALUES ('compra', :solicitud_id, :nombre_archivo, :ruta_archivo)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':solicitud_id', $id);
                $stmt->bindParam(':nombre_archivo', $file['name']);
                $stmt->bindParam(':ruta_archivo', $filepath);
                $stmt->execute();
                
                // Registrar actividad
                $actividad = new Actividad();
                $actividad->tipo_solicitud = 'compra';
                $actividad->solicitud_id = $id;
                $actividad->usuario_id = $_SESSION['user_id'];
                $actividad->actividad = "Comprobante de pago subido: " . $file['name'];
                $actividad->create();
                
                $_SESSION['success'] = 'Comprobante subido exitosamente';
            } else {
                $_SESSION['error'] = 'Error al subir el archivo';
            }
            
            header('Location: /public/index.php?page=compra&action=view&id=' . $id);
            exit;
        }
    }
}

class ServicioController {
    
    public function create() {
        AuthController::requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fecha_servicio = $_POST['fecha_servicio'];
            $hora_servicio = $_POST['hora_servicio'];
            $numero_personas = $_POST['numero_personas'] ?? 1;
            $amenidad_id = $_POST['amenidad_id'] ?? null;
            $descripcion_servicio = $_POST['descripcion_servicio'];
            $notas = $_POST['notas'] ?? '';
            
            // Validaciones
            if (empty($fecha_servicio) || empty($hora_servicio) || empty($descripcion_servicio)) {
                $_SESSION['error'] = 'Por favor complete todos los campos obligatorios';
                header('Location: /public/index.php?page=form&type=servicio');
                exit;
            }
            
            // Validar que la fecha no sea en el pasado
            if (strtotime($fecha_servicio) < strtotime(date('Y-m-d'))) {
                $_SESSION['error'] = 'No puede solicitar servicios para fechas pasadas';
                header('Location: /public/index.php?page=form&type=servicio');
                exit;
            }
            
            $servicio = new Servicio();
            $servicio->usuario_id = $_SESSION['user_id'];
            $servicio->fecha_servicio = $fecha_servicio;
            $servicio->hora_servicio = $hora_servicio;
            $servicio->numero_personas = $numero_personas;
            $servicio->amenidad_id = $amenidad_id;
            $servicio->descripcion_servicio = $descripcion_servicio;
            $servicio->notas = $notas;
            
            if ($servicio->create()) {
                // Registrar actividad
                $actividad = new Actividad();
                $actividad->tipo_solicitud = 'servicio';
                $actividad->solicitud_id = $servicio->id;
                $actividad->usuario_id = $_SESSION['user_id'];
                $actividad->actividad = "Servicio solicitado para $fecha_servicio a las $hora_servicio";
                $actividad->create();
                
                $_SESSION['success'] = 'Servicio solicitado exitosamente';
                header('Location: /public/index.php?page=dashboard&type=' . $_SESSION['user_type']);
                exit;
            } else {
                $_SESSION['error'] = 'Error al solicitar el servicio';
                header('Location: /public/index.php?page=form&type=servicio');
                exit;
            }
        }
        
        // Obtener amenidades disponibles
        $amenidad = new Amenidad();
        $amenidades = $amenidad->getAll();
        
        include __DIR__ . '/../views/forms/servicio.php';
    }
    
    public function view($id) {
        AuthController::requireAuth();
        
        $servicio = new Servicio();
        $data = $servicio->findById($id);
        
        if (!$data) {
            $_SESSION['error'] = 'Servicio no encontrado';
            header('Location: /public/index.php?page=dashboard&type=' . $_SESSION['user_type']);
            exit;
        }
        
        // Verificar permisos
        if ($_SESSION['user_type'] === 'cliente' && $data['usuario_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'No tiene permisos para ver este servicio';
            header('Location: /public/index.php?page=dashboard&type=cliente');
            exit;
        }
        
        // Obtener actividades
        $actividad = new Actividad();
        $actividades = $actividad->getBySolicitud('servicio', $id);
        
        // Obtener personal disponible para asignación
        $usuario = new Usuario();
        $personal = $usuario->getAllByType('comercio');
        
        include __DIR__ . '/../views/details/servicio.php';
    }
    
    public function uploadComprobante() {
        AuthController::requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['comprobante'])) {
            $id = $_POST['servicio_id'];
            $file = $_FILES['comprobante'];
            
            // Validar que el usuario tenga acceso a este servicio
            $servicio = new Servicio();
            $data = $servicio->findById($id);
            
            if (!$data || ($_SESSION['user_type'] === 'cliente' && $data['usuario_id'] != $_SESSION['user_id'])) {
                $_SESSION['error'] = 'Servicio no encontrado o sin permisos';
                header('Location: /public/index.php?page=dashboard&type=' . $_SESSION['user_type']);
                exit;
            }
            
            // Validar archivo
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
            if (!in_array($file['type'], $allowed_types)) {
                $_SESSION['error'] = 'Tipo de archivo no permitido. Use JPEG, PNG, GIF o PDF';
                header('Location: /public/index.php?page=servicio&action=view&id=' . $id);
                exit;
            }
            
            if ($file['size'] > 5 * 1024 * 1024) { // 5MB
                $_SESSION['error'] = 'El archivo es muy grande. Máximo 5MB';
                header('Location: /public/index.php?page=servicio&action=view&id=' . $id);
                exit;
            }
            
            // Crear directorio si no existe
            $upload_dir = UPLOAD_PATH . 'comprobantes/servicios/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Generar nombre único
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'servicio_' . $id . '_' . time() . '.' . $extension;
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Guardar en base de datos
                $conn = getDB();
                $query = "INSERT INTO comprobantes_pago (tipo_solicitud, solicitud_id, nombre_archivo, ruta_archivo) 
                         VALUES ('servicio', :solicitud_id, :nombre_archivo, :ruta_archivo)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':solicitud_id', $id);
                $stmt->bindParam(':nombre_archivo', $file['name']);
                $stmt->bindParam(':ruta_archivo', $filepath);
                $stmt->execute();
                
                // Registrar actividad
                $actividad = new Actividad();
                $actividad->tipo_solicitud = 'servicio';
                $actividad->solicitud_id = $id;
                $actividad->usuario_id = $_SESSION['user_id'];
                $actividad->actividad = "Comprobante de pago subido: " . $file['name'];
                $actividad->create();
                
                $_SESSION['success'] = 'Comprobante subido exitosamente';
            } else {
                $_SESSION['error'] = 'Error al subir el archivo';
            }
            
            header('Location: /public/index.php?page=servicio&action=view&id=' . $id);
            exit;
        }
    }
}