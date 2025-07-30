<?php

require_once __DIR__ . '/../models/Reservacion.php';
require_once __DIR__ . '/../models/Servicio.php';
require_once __DIR__ . '/AuthController.php';

class ReservacionController {
    
    public function create() {
        AuthController::requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fecha_reservacion = $_POST['fecha_reservacion'];
            $hora_reservacion = $_POST['hora_reservacion'];
            $numero_personas = $_POST['numero_personas'];
            $amenidad_id = $_POST['amenidad_id'] ?? null;
            $notas = $_POST['notas'] ?? '';
            
            // Validaciones
            if (empty($fecha_reservacion) || empty($hora_reservacion) || empty($numero_personas)) {
                $_SESSION['error'] = 'Por favor complete todos los campos obligatorios';
                header('Location: /public/index.php?page=form&type=reservacion');
                exit;
            }
            
            // Validar que la fecha no sea en el pasado
            if (strtotime($fecha_reservacion) < strtotime(date('Y-m-d'))) {
                $_SESSION['error'] = 'No puede hacer reservaciones para fechas pasadas';
                header('Location: /public/index.php?page=form&type=reservacion');
                exit;
            }
            
            $reservacion = new Reservacion();
            $reservacion->usuario_id = $_SESSION['user_id'];
            $reservacion->fecha_reservacion = $fecha_reservacion;
            $reservacion->hora_reservacion = $hora_reservacion;
            $reservacion->numero_personas = $numero_personas;
            $reservacion->amenidad_id = $amenidad_id;
            $reservacion->notas = $notas;
            
            if ($reservacion->create()) {
                // Registrar actividad
                $actividad = new Actividad();
                $actividad->tipo_solicitud = 'reservacion';
                $actividad->solicitud_id = $reservacion->id;
                $actividad->usuario_id = $_SESSION['user_id'];
                $actividad->actividad = "Reservación creada para $fecha_reservacion a las $hora_reservacion";
                $actividad->create();
                
                $_SESSION['success'] = 'Reservación creada exitosamente';
                header('Location: /public/index.php?page=dashboard&type=' . $_SESSION['user_type']);
                exit;
            } else {
                $_SESSION['error'] = 'Error al crear la reservación';
                header('Location: /public/index.php?page=form&type=reservacion');
                exit;
            }
        }
        
        // Obtener amenidades disponibles
        $amenidad = new Amenidad();
        $amenidades = $amenidad->getAll();
        
        include __DIR__ . '/../views/forms/reservacion.php';
    }
    
    public function view($id) {
        AuthController::requireAuth();
        
        $reservacion = new Reservacion();
        $data = $reservacion->findById($id);
        
        if (!$data) {
            $_SESSION['error'] = 'Reservación no encontrada';
            header('Location: /public/index.php?page=dashboard&type=' . $_SESSION['user_type']);
            exit;
        }
        
        // Verificar permisos
        if ($_SESSION['user_type'] === 'cliente' && $data['usuario_id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'No tiene permisos para ver esta reservación';
            header('Location: /public/index.php?page=dashboard&type=cliente');
            exit;
        }
        
        // Obtener actividades
        $actividad = new Actividad();
        $actividades = $actividad->getBySolicitud('reservacion', $id);
        
        // Obtener personal disponible para asignación
        $usuario = new Usuario();
        $personal = $usuario->getAllByType('comercio');
        
        include __DIR__ . '/../views/details/reservacion.php';
    }
    
    public function uploadComprobante() {
        AuthController::requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['comprobante'])) {
            $id = $_POST['reservacion_id'];
            $file = $_FILES['comprobante'];
            
            // Validar que el usuario tenga acceso a esta reservación
            $reservacion = new Reservacion();
            $data = $reservacion->findById($id);
            
            if (!$data || ($_SESSION['user_type'] === 'cliente' && $data['usuario_id'] != $_SESSION['user_id'])) {
                $_SESSION['error'] = 'Reservación no encontrada o sin permisos';
                header('Location: /public/index.php?page=dashboard&type=' . $_SESSION['user_type']);
                exit;
            }
            
            // Validar archivo
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
            if (!in_array($file['type'], $allowed_types)) {
                $_SESSION['error'] = 'Tipo de archivo no permitido. Use JPEG, PNG, GIF o PDF';
                header('Location: /public/index.php?page=reservacion&action=view&id=' . $id);
                exit;
            }
            
            if ($file['size'] > 5 * 1024 * 1024) { // 5MB
                $_SESSION['error'] = 'El archivo es muy grande. Máximo 5MB';
                header('Location: /public/index.php?page=reservacion&action=view&id=' . $id);
                exit;
            }
            
            // Crear directorio si no existe
            $upload_dir = UPLOAD_PATH . 'comprobantes/reservaciones/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Generar nombre único
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'reservacion_' . $id . '_' . time() . '.' . $extension;
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Guardar en base de datos
                $conn = getDB();
                $query = "INSERT INTO comprobantes_pago (tipo_solicitud, solicitud_id, nombre_archivo, ruta_archivo) 
                         VALUES ('reservacion', :solicitud_id, :nombre_archivo, :ruta_archivo)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':solicitud_id', $id);
                $stmt->bindParam(':nombre_archivo', $file['name']);
                $stmt->bindParam(':ruta_archivo', $filepath);
                $stmt->execute();
                
                // Registrar actividad
                $actividad = new Actividad();
                $actividad->tipo_solicitud = 'reservacion';
                $actividad->solicitud_id = $id;
                $actividad->usuario_id = $_SESSION['user_id'];
                $actividad->actividad = "Comprobante de pago subido: " . $file['name'];
                $actividad->create();
                
                $_SESSION['success'] = 'Comprobante subido exitosamente';
            } else {
                $_SESSION['error'] = 'Error al subir el archivo';
            }
            
            header('Location: /public/index.php?page=reservacion&action=view&id=' . $id);
            exit;
        }
    }
}