<?php

require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Reservacion.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Servicio.php';
require_once __DIR__ . '/AuthController.php';

class DashboardController {
    
    public function admin() {
        AuthController::requireRole('admin');
        
        // Obtener estadísticas generales
        $reservacion = new Reservacion();
        $servicio = new Servicio();
        $compra = new Compra();
        $usuario = new Usuario();
        
        $stats = [
            'reservaciones' => $reservacion->getEstadisticas(),
            'servicios' => $servicio->getEstadisticas(),
            'total_usuarios' => count($usuario->getAll()),
            'usuarios_comercio' => count($usuario->getAllByType('comercio')),
            'usuarios_cliente' => count($usuario->getAllByType('cliente'))
        ];
        
        // Obtener listas recientes
        $reservaciones_recientes = $reservacion->getAll();
        $compras_recientes = $compra->getAll();
        $servicios_recientes = $servicio->getAll();
        
        include __DIR__ . '/../views/dashboard/admin.php';
    }
    
    public function comercio() {
        AuthController::requireRole(['admin', 'comercio']);
        
        // Obtener estadísticas del comercio
        $reservacion = new Reservacion();
        $servicio = new Servicio();
        $compra = new Compra();
        
        $stats = [
            'reservaciones' => $reservacion->getEstadisticas(),
            'servicios' => $servicio->getEstadisticas()
        ];
        
        // Obtener solicitudes pendientes y recientes
        $reservaciones = $reservacion->getAll();
        $compras = $compra->getAll();
        $servicios = $servicio->getAll();
        
        include __DIR__ . '/../views/dashboard/comercio.php';
    }
    
    public function cliente() {
        AuthController::requireRole(['admin', 'comercio', 'cliente']);
        
        $usuario_id = $_SESSION['user_id'];
        
        // Obtener las solicitudes del cliente
        $reservacion = new Reservacion();
        $compra = new Compra();
        $servicio = new Servicio();
        
        $mis_reservaciones = $reservacion->getByUsuario($usuario_id);
        $mis_compras = $compra->getByUsuario($usuario_id);
        $mis_servicios = $servicio->getByUsuario($usuario_id);
        
        include __DIR__ . '/../views/dashboard/cliente.php';
    }
    
    public function updateSolicitud() {
        AuthController::requireRole(['admin', 'comercio']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo = $_POST['tipo'];
            $id = $_POST['id'];
            $estado = $_POST['estado'];
            $costo_total = $_POST['costo_total'] ?? 0;
            $personal_asignado_id = $_POST['personal_asignado_id'] ?? null;
            $notas = $_POST['notas'] ?? '';
            
            switch ($tipo) {
                case 'reservacion':
                    $obj = new Reservacion();
                    if ($obj->findById($id)) {
                        $obj->estado = $estado;
                        $obj->costo_total = $costo_total;
                        $obj->personal_asignado_id = $personal_asignado_id;
                        $obj->notas = $notas;
                        $obj->update();
                        
                        // Registrar actividad
                        $actividad = new Actividad();
                        $actividad->tipo_solicitud = 'reservacion';
                        $actividad->solicitud_id = $id;
                        $actividad->usuario_id = $_SESSION['user_id'];
                        $actividad->actividad = "Estado actualizado a: $estado";
                        $actividad->create();
                    }
                    break;
                    
                case 'compra':
                    $obj = new Compra();
                    if ($obj->findById($id)) {
                        $obj->estado = $estado;
                        $obj->personal_asignado_id = $personal_asignado_id;
                        $obj->notas = $notas;
                        $obj->update();
                        
                        // Registrar actividad
                        $actividad = new Actividad();
                        $actividad->tipo_solicitud = 'compra';
                        $actividad->solicitud_id = $id;
                        $actividad->usuario_id = $_SESSION['user_id'];
                        $actividad->actividad = "Estado actualizado a: $estado";
                        $actividad->create();
                    }
                    break;
                    
                case 'servicio':
                    $obj = new Servicio();
                    if ($obj->findById($id)) {
                        $obj->estado = $estado;
                        $obj->costo_total = $costo_total;
                        $obj->personal_asignado_id = $personal_asignado_id;
                        $obj->notas = $notas;
                        $obj->update();
                        
                        // Registrar actividad
                        $actividad = new Actividad();
                        $actividad->tipo_solicitud = 'servicio';
                        $actividad->solicitud_id = $id;
                        $actividad->usuario_id = $_SESSION['user_id'];
                        $actividad->actividad = "Estado actualizado a: $estado";
                        $actividad->create();
                    }
                    break;
            }
            
            $_SESSION['success'] = 'Solicitud actualizada correctamente';
        }
        
        // Redirigir de vuelta al dashboard
        if ($_SESSION['user_type'] === 'admin') {
            header('Location: ' . getUrl('index.php?page=dashboard&type=admin'));
        } else {
            header('Location: ' . getUrl('index.php?page=dashboard&type=comercio'));
        }
        exit;
    }
}